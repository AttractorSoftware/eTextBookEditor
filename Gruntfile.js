module.exports = function(grunt) {
    grunt.initConfig({
        concat: {
            main: {
                src: [
                    'js/lib/jquery-2.1.1.min.js'
                    ,'js/lib/bootstrap.min.js'
                    ,'js/lib/underscore-min.js'
                    ,'js/lib/backbone-min.js'
                    ,'js/app.js'
                    ,'js/eTextBook/*.js'
                    ,'js/eTextBook/inline/*.js'
                    ,'js/eTextBook/widget/*.js'
                ],
                dest: 'js/script.js'
            }
            ,template: {
                src: [
                    'js/lib/jquery-2.1.1.min.js'
                    ,'js/lib/underscore-min.js'
                    ,'js/lib/backbone-min.js'
                    ,'js/app.js'
                    ,'js/eTextBook/widget.js'
                    ,'js/eTextBook/widgetRepository.js'
                    ,'js/eTextBook/widget/*.js'
                    ,'js/html5Player.js'
                ],
                dest: 'template/js/script.js'
            }
        }
        ,concat_css: {
            main: {
                src: [
                    'css/bootstrap.min.css'
                    ,'css/style.css'
                ]
                ,dest: 'css/main-style.css'
            }
            ,template: {
                src: [
                    'css/bootstrap.min.css'
                    ,'template/css/style.css'
                    ,'css/html5Player.css'
                ]
                ,dest: 'template/css/main-style.css'
            }
        }
        ,cssmin: {
            main: {
                files: {
                    'css/main-style.min.css': ['css/main-style.css']
                }
            }
            ,template: {
                files: {
                    'template/css/main-style.min.css': ['template/css/main-style.css']
                }
            }
        }
        ,uglify: {
            main: {
                files: {
                    'js/script.min.js': '<%= concat.main.dest %>'
                }
            }
            ,template: {
                files: {
                    'template/js/script.min.js': '<%= concat.template.dest %>'
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-concat-css');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('default', ['concat', 'concat_css', 'cssmin', 'uglify']);

}