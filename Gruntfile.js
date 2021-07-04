/**
 * @project:   Addon list add-on for concrete5
 * 
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */
var packageName = "addon_list";

module.exports = function (grunt) {
    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),
        version: {
            php: {
                options: {
                    pkg: {
                        version: function() {
                            var s = grunt.file.read('controller.php');
                            var re = /\$pkgVersion[\s*]=[\s*][\'|\"](.*)[\'|\"]/g              
                            var m = re.exec(s);

                            if (m.length) {
                                return m[1];
                            }

                            return false;
                        }()
                    },
                    prefix: '@version\\s*'
                },
                src: [
                    'dist/*.php', 'dist/**/*.php', 'dist/**/**/*.php', 'dist/**/**/**/*.php', 'dist/**/**/**/**/*.php'
                ]
            }
        },
        composer: {
            options: {
                usePhp: true,
                composerLocation: './node_modules/getcomposer/composer.phar'
            },
            dev: {
                options: {
                    flags: ['ignore-platform-reqs']
                }
            },
            release: {
                options: {
                    flags: ['no-dev']
                }
            }
        },
        copy: {
            main: {
                files: [
                    {src: ['controller.php'], dest: "dist/", filter: 'isFile'},
                    {src: ['icon.png'], dest: "dist/", filter: 'isFile'},
                    {src: ['INSTALL.TXT'], dest: "dist/", filter: 'isFile'},
                    {src: ['LICENSE.TXT'], dest: "dist/", filter: 'isFile'},
                    {src: ['CHANGELOG'], dest: "dist/", filter: 'isFile'},
                    {src: ['languages/**'], dest: "dist/"},
                    {src: ['src/**'], dest: "dist/"},
                    {src: ['blocks/**'], dest: "dist/"},
                    {src: ['jobs/**'], dest: "dist/"},
                    {src: ['elements/**'], dest: "dist/"},
                    {src: ['controllers/**'], dest: "dist/"},
                    {src: ['vendor/**'], dest: "dist/"},
                    {src: ['images/**'], dest: "dist/"},
                    {src: ['single_pages/**'], dest: "dist/"},
                    {src: ['bower_components/**'], dest: "dist/"}
                ]
            }
        },
        compress: {
            main: {
                options: {
                    archive: 'build/' + packageName + '.zip'
                },
                files: [
                    {src: ['**'], dest: packageName, expand: true, cwd: 'dist/'}
                ]
            }
        },
        clean: {
            dist: ['dist'],
            composer: ['vendor', 'composer.lock']
        },
        phpcsfixer: {
            app: {
                dir: 'dist'
            },
            options: {
                bin: './vendor/friendsofphp/php-cs-fixer/php-cs-fixer',
                usingCache: "no",
                quiet: true
            }
        },
        po2mo: {
            de_DE: {
                src: 'languages/de_DE/LC_MESSAGES/messages.po',
                dest: 'languages/de_DE/LC_MESSAGES/messages.mo'
            }
        },
      
        concrete_package_uploader: {
            credentials: {
                username: grunt.option("username"),
                password: grunt.option("password")
            },

            packageInformations: {
                packageHandle: "addon_list",
                packageFile: "build/addon_list.zip",
                title: "Add-on list",
                version: function() {
                    var s = grunt.file.read('controller.php');
                    var re = /\$pkgVersion[\s*]=[\s*][\'|\"](.*)[\'|\"]/g              
                    var m = re.exec(s);

                    if (m.length) {
                        return m[1];
                    }

                    return false;
                }(),
                singlePrice: 100,
                skillLevel: "Intermediate",
                keywords: 'addons, addon, add-ons, add-on, marketplace, item, marketplace item, marketplace items, items, synchronize, sync, list, block element, block, fabianbitter',
                shortDescription: 'Integrate your own extensions from the concrete5 marketplace into your website with this powerful add-on. The Add-on list with its simple design and easy handling offers you a broad range of functions which you will profit from!',
                description: function() {
                    var showdown  = require('showdown');
                    var converter = new showdown.Converter();      
                    var text = converter.makeHtml(grunt.file.read("README.md"));      
                    return text + '<p>Have you already seen my other add-ons? Click <a href="https://www.concrete5.org/marketplace/addons/-/view/?submit_search=1&amp;search_keywords=fabianbitter">here</a> for more information.</p><p><a class="btn btn-primary" href="https://www.concrete5.org/marketplace/addons/-/view/?submit_search=1&amp;search_keywords=fabianbitter">See more of my addons</a></p>';
                }(),
                categories: [16, 1273, 45],
                screenshots: ["marketplace_screenshots/1.jpg", "marketplace_screenshots/2.jpg", "marketplace_screenshots/3.jpg"],
                supportPlan: 12,
                supportHostedOffsite: true,
                supportHostedOffsiteUrl: "https://bitbucket.org/fabianbitter/" + packageName + "/issues/new"
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-po2mo');
    grunt.loadNpmTasks('grunt-concrete-package-uploader');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-composer');
    grunt.loadNpmTasks('grunt-php-cs-fixer');
    grunt.loadNpmTasks('grunt-version');
    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.registerTask('default', [ 'clean:composer', 'composer:release:install', 'clean:dist', 'copy', 'version', 'clean:composer', 'composer:dev:install', 'phpcsfixer', 'compress:main', 'clean:dist']);
    grunt.registerTask('deploy', ['default', 'concrete_package_uploader']);
  
};