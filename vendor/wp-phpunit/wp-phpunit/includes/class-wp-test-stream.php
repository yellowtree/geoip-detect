<?php

/**
 * Class WP_Test_Stream.
 *
 * An in-memory streamWrapper implementation for testing streams.  Writes to a
 * stream URL like "protocol://bucket/foo" will be stored in the static
 * variable WP_Test_Stream::$data['bucket']['/foo'].
 *
 * Creating a directory at "protocol://bucket/foo" will store the string
 * 'DIRECTORY' to the static variable WP_Test_Stream::$data['bucket']['/foo/']
 * (note the trailing slash).
 *
 * This class can be used to test that code works with basic read/write streams,
 * as such, operations such as seeking are not supported.
 *
 * This class does not register itself as a stream handler: test fixtures
 * should make the appropriate call to stream_wrapper_register().
 */
class WP_Test_Stream {
	const FILE_MODE      = 0100666;
	const DIRECTORY_MODE = 040777;

	/**
	 * In-memory storage for files and directories simulated by this wrapper.
	 */
	static $data = array();

	var $position;
	var $file;
	var $bucket;
	var $data_ref;

	/**
	 * Initializes internal state for reading the given URL.
	 *
	 * @param string $url A URL of the form "protocol://bucket/path".
	 */
	private function open( $url ) {
		$components = array_merge(
			array(
				'host' => '',
				'path' => '',
			),
			parse_url( $url )
		);

		$this->bucket = $components['host'];
		$this->file   = $components['path'] ? $components['path'] : '/';

		if ( empty( $this->bucket ) ) {
			trigger_error( 'Cannot use an empty bucket name', E_USER_ERROR );
		}

		if ( ! isset( WP_Test_Stream::$data[ $this->bucket ] ) ) {
			WP_Test_Stream::$data[ $this->bucket ] = array();
		}

		$this->data_ref =& WP_Test_Stream::$data[ $this->bucket ][ $this->file ];

		$this->position = 0;
	}

	/**
	 * Opens a URL.
	 *
	 * @see streamWrapper::stream_open
	 */
	function stream_open( $path, $mode, $options, &$opened_path ) {
		$this->open( $path );
		return true;
	}

	/**
	 * Reads from a stream.
	 *
	 * @see streamWrapper::stream_read
	 */
	function stream_read( $count ) {
		if ( ! isset( $this->data_ref ) ) {
			return '';
		}

		$ret = substr( $this->data_ref, $this->position, $count );

		$this->position += strlen( $ret );
		return $ret;
	}

	/**
	 * Writes to a stream.
	 *
	 * @see streamWrapper::stream_write
	 */
	function stream_write( $data ) {
		if ( ! isset( $this->data_ref ) ) {
			$this->data_ref = '';
		}

		$left  = substr( $this->data_ref, 0, $this->position );
		$right = substr( $this->data_ref, $this->position + strlen( $data ) );

		WP_Test_Stream::$data[ $this->bucket ][ $this->file ] = $left . $data . $right;

		$this->position += strlen( $data );
		return strlen( $data );
	}

	/**
	 * Retrieves the current position of a stream.
	 *
	 * @see streamWrapper::stream_tell
	 */
	function stream_tell() {
		return $this->position;
	}

	/**
	 * Tests for end-of-file.
	 *
	 * @see streamWrapper::stream_eof
	 */
	function stream_eof() {
		if ( ! isset( $this->data_ref ) ) {
			return true;
		}

		return $this->position >= strlen( $this->data_ref );
	}

	/**
	 * Change stream metadata.
	 *
	 * @see streamWrapper::stream_metadata
	 */
	function stream_metadata( $path, $option, $var ) {
		$this->open( $path );
		if ( STREAM_META_TOUCH === $option ) {
			if ( ! isset( $this->data_ref ) ) {
				$this->data_ref = '';
			}
			return true;
		}
		return false;
	}

	/**
	 * Creates a directory.
	 *
	 * @see streamWrapper::mkdir
	 */
	function mkdir( $path, $mode, $options ) {
		$this->open( $path );
		$plainfile = rtrim( $this->file, '/' );

		if ( isset( WP_Test_Stream::$data[ $this->bucket ][ $file ] ) ) {
			return false;
		}
		$dir_ref = & $this->get_directory_ref();
		$dir_ref = 'DIRECTORY';
		return true;
	}

	/**
	 * Creates a file metadata object, with defaults.
	 *
	 * @param array $stats Partial file metadata.
	 * @return array Complete file metadata.
	 */
	private function make_stat( $stats ) {
		$defaults = array(
			'dev'     => 0,
			'ino'     => 0,
			'mode'    => 0,
			'nlink'   => 0,
			'uid'     => 0,
			'gid'     => 0,
			'rdev'    => 0,
			'size'    => 0,
			'atime'   => 0,
			'mtime'   => 0,
			'ctime'   => 0,
			'blksize' => 0,
			'blocks'  => 0,
		);

		return array_merge( $defaults, $stats );
	}

	/**
	 * Retrieves information about a file.
	 *
	 * @see streamWrapper::stream_stat
	 */
	public function stream_stat() {
		$dir_ref = & $this->get_directory_ref();
		if ( substr( $this->file, -1 ) === '/' || isset( $dir_ref ) ) {
			return $this->make_stat(
				array(
					'mode' => WP_Test_Stream::DIRECTORY_MODE,
				)
			);
		}

		if ( ! isset( $this->data_ref ) ) {
			return false;
		}

		return $this->make_stat(
			array(
				'size' => strlen( $this->data_ref ),
				'mode' => WP_Test_Stream::FILE_MODE,
			)
		);
	}

	/**
	 * Retrieves information about a file.
	 *
	 * @see streamWrapper::url_stat
	 */
	public function url_stat( $path, $flags ) {
		$this->open( $path );
		return $this->stream_stat();
	}

	/**
	 * Deletes a file.
	 *
	 * @see streamWrapper::unlink
	 */
	public function unlink( $path ) {
		if ( ! isset( $this->data_ref ) ) {
			return false;
		}
		unset( WP_Test_Stream::$data[ $this->bucket ][ $this->file ] );
		return true;
	}

	/**
	 * Interprets this stream's path as a directory, and returns the entry.
	 *
	 * @return A reference to the data entry for the directory.
	 */
	private function &get_directory_ref() {
		return WP_Test_Stream::$data[ $this->bucket ][ rtrim( $this->file, '/' ) . '/' ];
	}
}
