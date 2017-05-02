var gulp = require('gulp');
var sass = require('gulp-sass');
var csso = require('gulp-csso');
var minify = require('gulp-minify');


gulp.task('css', function(){
    return gulp.src('./public/src/css/*.scss')
        .pipe(sass())
        .pipe(csso())
        .pipe(gulp.dest('./public/src/css'))
});

gulp.task('js', function() {
    gulp.src('./public/src/js/*.js')
        .pipe(minify({
            ext:{
                src:'.js',
                min:'.min.js'
            },
            exclude: ['tasks'],
            ignoreFiles: ['.min.js']
        }))
        .pipe(gulp.dest('./public/src/js'))
});

gulp.task('default', [ 'css', 'js']);