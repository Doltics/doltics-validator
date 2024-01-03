/*global require*/

/**
 * When grunt command does not execute try these steps:
 *
 * - delete folder 'node_modules' and run command in console:
 *   $ npm install
 *
 * - Run test-command in console, to find syntax errors in script:
 *   $ grunt hello
 */

module.exports = function( grunt ) {
	// Show elapsed time at the end.
	require( 'time-grunt' )(grunt);

	// Load all grunt tasks.
	require( 'load-grunt-tasks' )(grunt);

	var buildtime = new Date().toISOString();

	var conf = {

		// BUILD branches.
		plugin_branches: {
			include_files: [
				'**',
				'!**/_src/**',
				'!**/_src/sass/**',
				'!**/_src/js/**',
				'!**/_src/react/**',
				'!**/img/src/**',
				'!**/node_modules/**',
				'!**/tests/**',
				'!**/release/*.zip',
				'!release/*.zip',
				'!**/release/**',
				'!**/Gruntfile.js',
				'!**/package.json',
				'!**/package-lock.json',
				'!**/build/**',
				'!_src/**',
				'!node_modules/**',
				'!.sass-cache/**',
				'!release/**',
				'!Gruntfile.js',
				'!package.json',
				'!package-lock.json',
				'!build/**',
				'!tests/**',
				'!.git/**',
				'!.git',
				'!**/.svn/**',
				'!.log',
				'!docs/phpdoc-**',
				'!vendor/**',
				'!webpack.config.js',
				'!**/webpack.config.js',
				'!postcss.config.js',
				'!**/postcss.config.js',
				'!composer.json',
				'!**/composer.json',
				'!composer.lock',
				'!**/composer.lock',
				'!phpcs.xml.dist',
				'!**/phpcs.xml.dist',
				'!phpunit.xml.dist',
				'!**/phpunit.xml.dist',
				'!gulpfile.js',
				'!**/gulpfile.js',
				'!jsconfig.json',
				'!**/jsconfig.json',
				'!README.md',
				'!**/README.md',
				'!phpcs.ruleset.xml',
				'!../phpcs.ruleset.xml',
				'!LICENSE',
				'!../LICENSE',
				'!bin/**',
				'!../bin/**'
			]
		},

		// Regex patterns to exclude from transation.
		translation: {
			ignore_files: [
				'node_modules/.*',
				'(^.php)',      // Ignore non-php files.
				'lib/.*',       // External libraries.
				'release/.*',   // Temp release files.
				'tests/.*',     // Unit testing.
				'docs/.*',      // API Documentation.
			],
			pot_dir: 'languages/', // With trailing slash.
			textdomain: 'doltics-validator',
		},

		plugin_dir: 'doltics-validator/',
		plugin_file: 'doltics-validator.php',
	};

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),

		// JS - Concat .js source files into a single .js file.
		concat: {
			options: {
				stripBanners: true,
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
					' * <%= pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
					' * Licensed GPLv2+' +
					' */\n'
			}
		},


		// BUILD - Remove previous build version and temp files.
		clean: {
			temp: {
				src: [
					'**/*.tmp',
					'**/.afpDeleted*',
					'**/.DS_Store'
				],
				dot: true,
				filter: 'isFile'
			}
		},

		// BUILD - Copy all plugin files to the release subdirectory.
		copy: {
            files: {
                src: conf.plugin_branches.include_files,
                dest: 'release/<%= pkg.name %>-<%= pkg.version %>/'
            }
		},

		// BUILD - Create a zip-version of the plugin.
		compress: {
            files: {
                options: {
                    mode: 'zip',
                    archive: './release/<%= pkg.name %>-<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'release/<%= pkg.name %>-<%= pkg.version %>/',
                src: [ '**/*' ],
                dest: conf.plugin_dir
            }
		},

		// BUILD - update the translation index .po file.
		makepot: {
			target: {
				options: {
					cwd: '',
					domainPath: conf.translation.pot_dir,
					exclude: conf.translation.ignore_files,
					mainFile: conf.plugin_file,
					potFilename: conf.translation.textdomain + '.pot',
					potHeaders: {
						poedit: true, // Includes common Poedit headers.
						'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
					},
					type: 'wp-plugin' // wp-plugin or wp-theme
				}
			}
		},

		// DOCS - Execute custom command to build the phpdocs for API
		exec: {
			phpdoc: {
				command: 'phpdoc -d ./app -t ./docs'
			}
		}

	} );

	// Test task.
	grunt.registerTask( 'hello', 'Test if grunt is working', function() {
		grunt.log.subhead( 'Hi there :)' );
		grunt.log.writeln( 'Looks like grunt is installed!' );
	});

	// Plugin build tasks
	grunt.registerTask( 'build', 'Run all tasks.', function(target) {


		// First run unit tests.
		//grunt.task.run( 'phpunit' );

		// Run the default tasks (js/css/php validation).
		grunt.task.run( 'default' );

		// Generate all translation files (same for pro and free).
		grunt.task.run( 'makepot' );

		// Update the integrated API documentation.
		//grunt.task.run( 'docs' );

		grunt.task.run( 'clean' );
		grunt.task.run( 'copy' );
		grunt.task.run( 'compress' );
		
	});

	// Development tasks.
	grunt.registerTask( 'default', ['clean:temp'] );
	grunt.registerTask( 'test' );
	grunt.registerTask( 'docs', ['exec:phpdoc'] );

	grunt.util.linefeed = '\n';
};