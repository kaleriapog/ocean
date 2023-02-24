// Loading modules
const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const cleanCSS = require('gulp-clean-css');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const csscomb = require('gulp-csscomb');

// Path Configuration
const paths = {
    styles: {
        src: './scss/style.scss',
        dest: './'
    },
    scripts: {
        src: './js/scripts/*.js',
        dest: './js/'
    }
};
// Compiling SCSS
function styles() {
    return gulp.src(paths.styles.src)
        .pipe(sass())
        .pipe(cleanCSS())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(paths.styles.dest));
}
// Compiling JavaScript
function scripts() {
    return gulp.src(paths.scripts.src)
        .pipe(concat('scripts.js'))
        .pipe(uglify())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(paths.scripts.dest));
}
// Keeping an eye on changes in SCSS and JavaScript
function watch() {
    gulp.watch(paths.styles.src, styles);
    gulp.watch(paths.scripts.src, scripts);
}
// csscomb
gulp.task('csscomb', function() {
    return gulp.src('./scss/_post.scss')
        .pipe(csscomb())
        .pipe(gulp.dest('./scss/i/'));
});

// Exporting tasks
exports.styles = styles;
exports.scripts = scripts;
exports.watch = watch;
exports.default = gulp.series(styles, scripts, watch);


