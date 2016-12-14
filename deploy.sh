#!/bin/bash
# Original of this script: https://github.com/thenbrent/multisite-user-management/blob/master/deploy.sh
# A modification of Dean Clatworthy's deploy script as found here: https://github.com/deanc/wordpress-plugin-git-svn
# The difference is that this script lives in the plugin's git repo & doesn't require an existing SVN repo.

# main config
PLUGINSLUG="geoip-detect"
CURRENTDIR=`pwd`
MAINFILE="geoip-detect.php" # this should be the name of your main php file in the wordpress plugin

# git config
GITPATH="$CURRENTDIR/" # this file should be in the base of your git repository

# svn config
SVNPATH="/tmp/$PLUGINSLUG" # path to a temp SVN repo. No trailing slash required and don't add trunk.
SVNURL="http://plugins.svn.wordpress.org/geoip-detect/" # Remote SVN repo on wordpress.org, with no trailing slash
SVNUSER="benjamin4" # your svn username


if [ "$1" = "checkout" ] ; then
	echo "Only Checkout"
	echo 
	echo "Creating local copy of SVN repo ..."
	svn co $SVNURL -N $SVNPATH
	svn up $SVNPATH/trunk
	svn up --set-depth empty $SVNPATH/tags

	echo "SVN Repo was checked out to $SVNPATH"
	exit 0;
fi

# Let's begin...
echo ".........................................."
echo 
echo "Preparing to deploy wordpress plugin"
echo 
echo ".........................................."
echo 

# Check version in readme.txt is the same as plugin file
#NEWVERSION1=`grep "^Stable tag" $GITPATH/readme.txt | awk -F' ' '{print $3}'`
#echo "readme version: $NEWVERSION1"
NEWVERSION2=`grep "^Version" $GITPATH/$MAINFILE | awk -F' ' '{print $2}'`
NEWVERSION="$NEWVERSION2"

echo "$MAINFILE version: $NEWVERSION2"
NEWVERSION3=`grep "^define.*GEOIP_DETECT_VERSION" $GITPATH/$MAINFILE | awk -F"'" '{print $4}'`
echo "$MAINFILE define version: $NEWVERSION3"

# if [ "$NEWVERSION1" != "$NEWVERSION2" ] || [ "$NEWVERSION1" != "$NEWVERSION3" ]; then echo "Versions don't match. Exiting...."; exit 1; fi
if [ "$NEWVERSION2" != "$NEWVERSION3" ]; then echo "Versions don't match. (php: '$NEWVERSION2', define: '$NEWVERSION3') Exiting...."; exit 1; fi

echo "Versions match in PHP file. Let's proceed..."

#echo "Compressing JS files..."
#java -jar ~/bin/yuicompressor.jar --nomunge --preserve-semi -o "$GITPATH/tinymce/editor_plugin.js" $GITPATH/tinymce/editor_plugin_src.js
#java -jar ~/bin/yuicompressor.jar --nomunge --preserve-semi -o "$GITPATH/tinymce/wpcf-select-box.js" $GITPATH/tinymce/wpcf-select-box_src.js

cd $GITPATH

echo "Generate README.md from readme.txt"
bin/readme.sh "$SVNURL"

echo -e "Enter a commit message for this new version: \c"
read COMMITMSG
git commit -am "$COMMITMSG"

echo "Tagging new version in git"
git tag -a "$NEWVERSION" -m "Tagging version $NEWVERSION"

echo "Pushing latest commit to origin, with tags"
git push origin --all
git push origin master --tags

echo 
echo "Creating local copy of SVN repo ..."
svn co $SVNURL -N $SVNPATH
svn up $SVNPATH/trunk
svn up --set-depth empty $SVNPATH/tags

echo "Exporting the HEAD of master from git to the trunk of SVN"
git checkout-index -a -f --prefix=$SVNPATH/trunk/

echo "Ignoring github specific files, tests and deployment script"
svn propset svn:ignore "deploy.sh
bin
README.md
.git
.gitignore
tests
test
phpunit.xml" "$SVNPATH/trunk/"

svn propset svn:ignore '*' "$SVNPATH/trunk/lib/geonames/generators/"

#if submodule exist, recursively check out their indexes (from benbalter)
if [ -f ".gitmodules" ]
then
echo "Exporting the HEAD of each submodule from git to the trunk of SVN"
git submodule init
git submodule update
git submodule foreach --recursive 'git checkout-index -a -f --prefix=$SVNPATH/trunk/$path/'
fi

echo "Changing directory to SVN and adding new files, if any"
cd $SVNPATH/trunk/
# Add all new files that are not set to be ignored
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs svn add
echo "Committing to trunk"
svn commit --username=$SVNUSER -m "$COMMITMSG"

echo "Creating new SVN tag & committing it"
cd $SVNPATH
svn copy trunk/ tags/$NEWVERSION/
cd $SVNPATH/tags/$NEWVERSION
svn commit --username=$SVNUSER -m "Tagging version $NEWVERSION"

echo "Removing temporary directory $SVNPATH"
rm -fr $SVNPATH/

echo "*** FIN ***"

