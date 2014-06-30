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
                    ,'js/eTextBook/templates/*.js'
                    ,'js/eTextBook/inline/*.js'
                    ,'js/eTextBook/widget/*.js'
                ],
                dest: 'js/script.js'
            }
        },
        uglify: {
            main: {
                files: {
                    'js/script.min.js': '<%= concat.main.dest %>'
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('default', ['concat', 'uglify']);
}