---
title: "To Developers"
---
So you are a developer looking on how to get started to modify this plugin? Great! Here are some hints:

## About the project

### Unsure if your pull request will be accepted?

Generally, I am very open to accept contributions, and to help those who take the time contributing. Here are some general guidelines:

* **I don't know what could be needed?** Look at the [issues](https://github.com/yellowtree/geoip-detect/issues) - I have a lot of feature ideas but only limited time to implement them! So if you find something intriguing and you are motivated to code it, just write me on that particular Github issue.
* **Is your (planned) change helpful to other people?** If it is outside the scope of this project / too specific to your use case, it is better you use the Wordpress filter system to modify the behavior of the plugin, maybe adding filters via PR if needed. 
* **Is it a small fix or a big feature?** If it's a small fix, the process will be quick as well. If you plan on changing/adding a lot of code, it would be good to plan it together before you start out - just raise an [issue](https://github.com/yellowtree/geoip-detect/issues) for this.
* **Is it backwards compatible?** All changes must be as backwards compatible as possible. If it breaks BC, it might still be important enough to release a new major version of it, but do talk to me first. 
* **Can I join the project long-term?** Why not. If you become a regular contributor, we can talk about you becoming a maintainer/committer as well. 

### Scope of the project

The target audience of this plugin are the *Wordpress coders*. They can use the functionality provided by this plugin on their (clients'?) sites, in their plugins or themes, etc. In some cases, integrating into other standard plugins is part of this project, but most of the time, this plugin will *be integrated* and thus, has to have a stable and documented API. (Only functions in [api.php](https://github.com/yellowtree/geoip-detect/blob/master/api.php), and documented Wordpress filters, are deemed official API.)

However, more and more also non-coders are using this plugin. That's ok, as it can be helpful to them as well (for example via the shortcodes) - and if I find ways of decreasing the number of support requests I get from them, that's great - but they are not my main audience. End-Users would need a simplified UI that helps them set up the plugin (the issues I get most contacted about are *reverse proxies* and *site caches*), so if you want to create a frontend for this, that would be great. It's not my priority, though. 

## About coding

### General procedure

* Fork the github repository into your github user
* Clone your repo fork in order to work on it locally
* Commit your changes in small commits / logical units. Don't worry about creating too many commits!
* Once you finished (or want my feedback), do consider sending me a [pull request](https://github.com/yellowtree/geoip-detect/pulls) so that others can benefit of them as well!

## Phpunit tests

After each commit that is uploaded to the repo, Travis executes all tests. (So if it is too complicated for you too install this locally, just create the pull request and wait for the travis tests to execute.)

If you want to execute them locally:

* Create a database with name, user and password called 'geoip_detect_phpunit' (or set the environment variables WP_DB_NAME, WP_DB_USER, WP_DB_PASS)
* Execute `composer test-install` once
* Each time you want to run the unit tests, execute `composer test`

It should show something like that at the end of the output:

```
OK (165 tests, 5434 assertions)
```

## JS / Ajax Mode

When editing the JS files, you need to recompile it.

Do this once (after installing [yarn](https://yarnpkg.com/)):
```
yarn install
```

While testing your changes, start this and leave it open
```
yarn start 
```

Then run this command to create a production version of the final JS:
```
yarn build
```

Then you can commit your changes.

### Jest Unit Tests

After setting up the JS dev environment (see above), simply run:

```
yarn test
```

## Composer

The composer PHP dependencies are also committed to the git project (because they need to be present in the Wordpress SVN repo as well), so you only need to install composer if you need to add a dependency there.
