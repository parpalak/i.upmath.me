module.exports = function(grunt) {

	grunt.initConfig({
		concat: {
			main: {
				src: [
					'node_modules/autosize/dist/autosize.min.js',
					'www/js/init_editor.js'
				],
				dest: 'www/js/scripts.js'
			}
		},
		uglify: {
			main: {
				files: {
					'www/js/scripts.min.js': '<%= concat.main.dest %>'
				}
			}
		},
		cssmin: {
			target: {
				src: [
					'www/css/style.css',
				],
				dest: 'www/css/style.min.css'
			}
		},
		fingerprint: {
			assets: {
				src: [
					'www/js/*.js',
					'www/css/*.css'
				],
				filename: 'fingerprint.php',
				template: "<?php define('FINGERPRINT', '<%= fingerprint %>'); ?>"
			}
		},
		shell: {
			gzipJS: {
				command: [
					'gzip -cn6 www/js/scripts.min.js > www/js/scripts.min.js.gz',
					'gzip -cn6 www/css/style.min.css > www/css/style.min.css.gz'
				].join(' && ')
			},
			gzipPublic: {
				command: 'gzip -cn6 www/latex.js > www/latex.js.gz'
			}
		},
		replace: {
			example: {
				src: ['src/latex.js'],
				dest: 'www/latex.js',
				replacements: [{
					from: 'i.upmath.me',
					to: function (matchedWord, index, fullText, regexMatches) {
						const host = __dirname.split('/').pop();

						return host !== 'tex.s2cms.ru' ? host : 'i.upmath.me';
					}
				}]
			}
		},
		watch: {
			scripts: {
				files: ['www/js/*.js', 'www/css/*.css'],
				tasks: ['concat', 'cssmin', 'uglify', 'fingerprint', 'shell:gzipJS'],
				options: {
					spawn: false
				}
			},
			src: {
				files: ['src/latex.js'],
				tasks: ['replace', 'shell:gzipPublic'],
				options: {
					spawn: false
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-fingerprint');
	grunt.loadNpmTasks('grunt-shell');
	grunt.loadNpmTasks('grunt-text-replace');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', [
		'concat',
		'uglify',
		'cssmin',
		'replace',
		'shell',
		'fingerprint'
	]);
};
