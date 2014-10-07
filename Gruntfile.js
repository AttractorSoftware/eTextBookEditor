module.exports = function (grunt) {
    grunt.initConfig({
        concat: {
            main: {
                src: [
                    'bower_components/jquery/dist/jquery.min.js'
                    , 'bower_components/jquery.fileapi/FileAPI/FileAPI.min.js'
                    , 'bower_components/jquery.fileapi/jquery.fileapi.min.js'
                    , 'bower_components/bootstrap/dist/js/bootstrap.min.js'
                    , 'bower_components/underscore/underscore.js'
                    , 'bower_components/backbone/backbone.js'
                    , 'bower_components/angularjs/angular.min.js'
                    , 'bower_components/summernote/dist/summernote.min.js'
                    , 'bower_components/anijs/dist/anijs-min.js'
                    , 'bower_components/anijs/dist/helpers/scrollreveal/anijs-helper-scrollreveal-min.js'
                    , 'web/js/app.js'
                    , 'web/js/eTextBook/objectStorage.js'
                    , 'web/js/eTextBook/*.js'
                    , 'web/js/eTextBook/inline/*.js'
                    , 'web/js/eTextBook/widget/*.js'
                ],
                dest: 'web/js/script.js'
            }
            ,template: {
                src: [
                    'bower_components/jquery/dist/jquery.min.js'
                    , 'bower_components/underscore/underscore.js'
                    , 'bower_components/backbone/backbone.js'
                    , 'bower_components/anijs/dist/anijs-min.js'
                    , 'bower_components/anijs/dist/helpers/scrollreveal/anijs-helper-scrollreveal.js'
                    , 'web/js/app.js'
                    , 'web/js/eTextBook/objectStorage.js'
                    , 'web/js/eTextBook/bookNavigation.js'
                    , 'web/js/eTextBook/utils.js'
                    , 'web/js/eTextBook/widget.js'
                    , 'web/js/eTextBook/widgetRepository.js'
                    , 'web/js/eTextBook/widget/*.js'
                    , 'web/js/html5Player.js'
                    , 'web/js/eTextBook/templateFormat.js'
                ],
                dest: 'web/book-template/js/script.js'
            }
        }, concat_css: {
            main: {
                src: [
                    'bower_components/bootstrap/dist/css/bootstrap.min.css'
                    ,'bower_components/summernote/dist/summernote.css'
                    ,'bower_components/components-font-awesome/css/font-awesome.min.css'
                    ,'bower_components/animate.css/animate.min.css'
                    , 'web/css/style.css'
                ], dest: 'web/css/main-style.css'
            }
            ,template: {
                src: [
                    'bower_components/bootstrap/dist/css/bootstrap.min.css'
                    ,'bower_components/animate.css/animate.min.css'
                    ,'web/css/style.css'
                    ,'web/css/lilac-theme/css/style.css'
                ], dest: 'web/book-template/css/main-style.css'
            }
        }, cssmin: {
            main: {
                files: {
                    'web/css/main-style.min.css': ['web/css/main-style.css']
                }
            }
            ,template: {
                files: {
                    'web/book-template/css/main-style.min.css': ['web/book-template/css/main-style.css']
                }
            }
        }, uglify: {
            main: {
                files: {
                    'web/js/script.min.js': '<%= concat.main.dest %>'
                }
            }
            ,template: {
                files: {
                    'web/book-template/js/script.min.js': '<%= concat.template.dest %>'
                }
            }
        }
    });

    require('load-grunt-tasks')(grunt);
    require('time-grunt')(grunt);

    grunt.registerTask('default', ['newer:concat', 'newer:concat_css', 'newer:cssmin', 'newer:uglify']);
    grunt.registerTask('dev', ['newer:concat', 'newer:concat_css', 'newer:cssmin']);
    grunt.registerTask('full', ['concat', 'concat_css', 'cssmin', 'uglify']);

};