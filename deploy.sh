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
SVNUSER="benjaminpick" # your svn username


# This also changes the current branch to MERGE_TO
function merge_branch_and_checkout()
{
	local MERGE_FROM=$1
	local MERGE_TO=$2

	echo
	echo "Merging $MERGE_FROM into $MERGE_TO ..."
	git checkout "$MERGE_TO" && git merge --ff-only "$MERGE_FROM"
	if [ $? != 0 ] ; then 
		echo "No merge possible with fast-forward, please merge $MERGE_FROM into $MERGE_TO manually ..."
		echo
		exit 1;
	fi
	echo 
}

useConfirm=true

confirm() {
   [ "$useConfirm" = true ] && read -p "Do you want to continue? (Enter) - (^C to abort)"
}


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

if [ "$1" = "checkout-with-tags" ] ; then
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
echo "Start the deploying process ..."
echo 
echo ".........................................."
echo 

# Check version in readme.txt is the same as plugin file
NEWVERSION=`grep "^Version" $GITPATH/$MAINFILE | awk -F' ' '{print $2}'`
echo "$MAINFILE header version: $NEWVERSION"
NEWVERSION2=`grep "^define.*GEOIP_DETECT_VERSION" $GITPATH/$MAINFILE | awk -F"'" '{print $4}'`
echo "$MAINFILE define version: $NEWVERSION2"
OLDVERSION=`grep "^Stable tag:" $GITPATH/readme.txt | awk -F' ' '{print $NF}'`
echo "readme.txt Stable (old) version: $OLDVERSION"
echo

if [ "$NEWVERSION" != "$NEWVERSION2" ] ; then echo "Oh no! Versions don't match. Exiting...."; exit 1; fi
if [ "$NEWVERSION" == "$OLDVERSION" ] ;  then echo "Oh no! Versions match (trunk must be smaller than new version). Exiting...."; exit 1; fi
if [ "trunk" == "$OLDVERSION" ] ;  then echo "Oh no! Stable tag is on trunk. Use the currently live version instead. Exiting...."; exit 1; fi

echo "Versions match in PHP file. Let's proceed..."

BETA=0
case "$NEWVERSION" in
	*beta*)
		BETA=1
		;;
esac
if [ "$1" = "beta" ] ; then 
	BETA=1 
fi


if [ "$BETA" = 1 ] ; then 
	echo
	echo "Releasing Beta version only."
	echo
fi

if [ `git branch --show-current` = "beta" ] ; then
	echo
	echo "Already on beta branch. If you continue, changes from the develop branch will not be merged in."
	echo
	confirm 
else
	echo "Not on beta branch, merging develop into beta..."
	merge_branch_and_checkout develop beta
fi

cd $GITPATH

echo "Re-generate JS ..."
rm -rf js/dist
yarn install && yarn clean && yarn build && git add js/dist
if [ $? != 0 ]; then echo ; echo "Yarn Failed."; echo ; exit 1; fi 

echo "Run Phpunit tests ..."
composer install-test
composer test 
if [ $? != 0 ]; then echo ; echo "Phpunit Failed."; echo ; exit 1; fi 
composer test-external
if [ $? != 0 ]; then echo ; echo "Phpunit for HTTP requests failed."; confirm ; fi 

echo "Set composer for production use ..."
composer install-prod
git checkout vendor

echo "Generate README.md from readme.txt"
bin/readme.sh "$SVNURL"
bin/changelog.sh

COMMITMSG_DEFAULT="Release $NEWVERSION"
git status
echo -e "Enter a commit message for this new version (default: $COMMITMSG_DEFAULT): \c"
read COMMITMSG
: ${COMMITMSG:=$COMMITMSG_DEFAULT}
git add vendor/composer/platform_check.php
git commit -am "$COMMITMSG"

if [ "$BETA" = 1 ] ; then
	# Merging back into develop
	merge_branch_and_checkout beta develop

	echo "Pushing latest commit to origin"
	git push origin --all

	git checkout develop
	echo 
	echo "OK. Beta version released."
	echo
	exit 0;
fi

# Merging all changes to master, then continue in master
merge_branch_and_checkout beta master

git push origin master 

# ---------------------- now updating SVN -----------------------

echo
echo "--------------------------------------------------"
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
docs
README.md
CHANGELOG.md
.git
.gitignore
.githooks
tests
test
phpunit.xml
babel.config.js
jest.config.js
renovate.json
parcel.urls
composer.lock
yarn.lock
vendor/symfony/property-access/Tests
vendor/phpspec
vendor/phpunit
vendor/wp-phpunit
vendor/webmozart
vendor/theseer
vendor/sebastian
vendor/yoast/phpunit-polyfills
vendor/nikic/php-parser
" "$SVNPATH/trunk/"

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
composer dump -o
# Add all new files that are not set to be ignored
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs svn add
echo "Committing to trunk"
svn commit --username=$SVNUSER -m "$COMMITMSG"
if [ $? != 0 ]; then echo ; echo "Error while committing to TRUNK."; confirm ; fi 

echo "Creating new SVN tag & committing it"
cd $SVNPATH
svn copy trunk/ tags/$NEWVERSION/
cd $SVNPATH/tags/$NEWVERSION
svn commit --username=$SVNUSER -m "Tagging version $NEWVERSION"
if [ $? != 0 ]; then echo ; echo "Error while committing to TAGS."; confirm ; fi 


echo
echo "Last chance to verify everything before it's really live ..."
confirm

function set_stable_tag_in_readme() {
	local NEW_VERSION=$1
	sed -i "s/^Stable tag:\s.*$/Stable tag: $NEW_VERSION/g" readme.txt
}

cd $SVNPATH/trunk
set_stable_tag_in_readme $NEWVERSION
cd $SVNPATH/tags/$NEWVERSION
set_stable_tag_in_readme $NEWVERSION
svn commit --username=$SVNUSER -m "Setting stable tag to $NEWVERSION"


echo "Tagging new version in git"
cd "$CURRENTDIR"
git tag -a "$NEWVERSION" -m "Tagging version $NEWVERSION"
git push origin master --tags

echo "Removing temporary directory $SVNPATH"
rm -fr $SVNPATH/

# Merging back into develop
merge_branch_and_checkout beta develop

echo "Pushing latest commit to origin"
git push origin --all

git checkout develop

echo "Composer Autoload back to dev"
composer dump
git commit -a -m "After Deployment"
echo "---- FIN ----"

