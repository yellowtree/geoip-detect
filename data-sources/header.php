<?php
/*
Copyright 2013-2016 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (info@yellowtree.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace YellowTree\GeoipDetect\DataSources\Header;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

class HeaderReader extends \YellowTree\GeoipDetect\DataSources\AbstractReader {
	
	protected $providers = array(
		'aws' => 'Amazon AWS CloudFront',
		'cloudflare' => 'Cloudflare',
	);
	
	protected $country_names = array
	(
		'AF' => 'Afghanistan',
		'AX' => 'Aland Islands',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua And Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia And Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei Darussalam',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CG' => 'Congo',
		'CD' => 'Congo, Democratic Republic',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => 'Cote D\'Ivoire',
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands (Malvinas)',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard Island & Mcdonald Islands',
		'VA' => 'Holy See (Vatican City State)',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran, Islamic Republic Of',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IM' => 'Isle Of Man',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KR' => 'Korea',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Lao People\'s Democratic Republic',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libyan Arab Jamahiriya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macao',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia, Federated States Of',
		'MD' => 'Moldova',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestinian Territory, Occupied',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russian Federation',
		'RW' => 'Rwanda',
		'BL' => 'Saint Barthelemy',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts And Nevis',
		'LC' => 'Saint Lucia',
		'MF' => 'Saint Martin',
		'PM' => 'Saint Pierre And Miquelon',
		'VC' => 'Saint Vincent And Grenadines',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome And Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia And Sandwich Isl.',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard And Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad And Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks And Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'GB' => 'United Kingdom',
		'US' => 'United States',
		'UM' => 'United States Outlying Islands',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VE' => 'Venezuela',
		'VN' => 'Viet Nam',
		'VG' => 'Virgin Islands, British',
		'VI' => 'Virgin Islands, U.S.',
		'WF' => 'Wallis And Futuna',
		'EH' => 'Western Sahara',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	);
	
	
	public function country($ip) {
		
		$r = array();

		$isoCode = '';
		switch ($this->options['provider']) {
			case 'aws':
				$isoCode = @$_SERVER['CloudFront-Viewer-Country'];
				break;
				
			case 'cloudflare';				
				$isoCode = @$_SERVER["HTTP_CF_IPCOUNTRY"];
				if ($isoCode == 'xx')
					$isoCode = '';	
				break;
		}
		$country = '';
		if (!$isoCode) {
			return null;
		}
		
		$r['country']['iso_code'] = strtoupper($isoCode);
		
		if (isset($this->country_names[ $r['country']['iso_code'] ]))
			$country = $this->country_names[ $r['country']['iso_code'] ];
			
		$r['country']['names'] = array('en' => $country);
		
		
		$r['traits']['ip_address'] = $ip;
		
		$record = new \GeoIp2\Model\City($r, array('en'));
		
		return $record;
	}
}

class HeaderDataSource extends AbstractDataSource {

	public function getId() { return 'header'; }
	public function getLabel() { return __('Special Hosting Providers (Cloudflare, Amazon AWS CloudFront)', 'geoip-detect'); }

	public function getDescriptionHTML() { return __('These hosting providers already do geodetection, but only of the visitor\'s country.', 'geoip-detect'); }
	
	public function getStatusInformationHTML() {
		$provider = get_option('geoip-detect-header-provider');
		
		$html = $this->getShortLabel();
		
		if ($provider == 'cloudflare') {
			$html .= '<br><br>';
			$html .= sprintf(__('This needs to be enabled in the admin panel: see <a href="%s">Help</a>.', 'geoip-detect'), 'https://support.cloudflare.com/hc/en-us/articles/200168236-What-does-CloudFlare-IP-Geolocation-do-');
		} elseif ($provider == 'aws') {
			$html .= '<br><br>';
			$html .= sprintf(__('This needs to be enabled in the admin panel: see <a href="%s">Help</a>.', 'geoip-detect'), 'https://aws.amazon.com/blogs/aws/enhanced-cloudfront-customization/');
		}
		return $html;
	}

	public function getParameterHTML() { 
		$provider = get_option('geoip-detect-header-provider');
		$checked_cloudflare = $provider == 'cloudflare' ? 'checked' : '';
		$checked_aws =        $provider == 'aws'        ? 'checked' : '';

		$label = __('Which Hosting Provider:', 'geoip-detect');
		$html = <<<HTML
		<p>$label<br> 
			<label><input type="radio" name="options_header[provider]" value="cloudflare" $checked_cloudflare /> Cloudflare</label>
			<label><input type="radio" name="options_header[provider]" value="aws" $checked_aws /> Amazon AWS CloudFront</label>
	    </p>
		<br />	
HTML;
		
		return $html;
	}
	
	public function saveParameters($post) {
		$message = '';
		
		$value = @$post['options_header']['provider'];
		if (!empty($value)) {
			update_option('geoip-detect-header-provider', $value);
		}
		
		return $message;
	}
	
	public function getShortLabel() {
		$provider = get_option('geoip-detect-header-provider');
		$labels = array(
			'' => __('None', 'geoip-detect'),
			'aws' => 'Amazon AWS CloudFront',
			'cloudflare' => 'Cloudflare',
		);
		if (!isset($labels[$provider]))
			$provider = '';
		
		$html = __('Hosting Provider: ', 'geoip-detect') . $labels[$provider];	
		
		return $html;
	}

	public function getReader($locales = array('en'), $options = array()) {
		$reader = null;
		
		$provider = get_option('geoip-detect-header-provider');
		if ($provider) {
			try {
				$reader = new HeaderReader( array(
					'provider' => $provider,
				) );
			} catch ( \Exception $e ) {
				if (WP_DEBUG)
					echo printf(__('Error while creating reader for "%s": %s', 'geoip-detect'), $filename, $e->getMessage ());
			}
		}
		
		return $reader;
	}

	public function isWorking() { 
		$working = (bool) get_option('geoip-detect-header-provider');

		return $working;
	}
	
}

geoip_detect2_register_source(new HeaderDataSource());
