var gulp = require('gulp'),
	concat = require('gulp-concat'),
	uglifycss = require('gulp-uglifycss'),
	watch = require('gulp-watch'),
	autoprefixer = require('gulp-autoprefixer'),
	cssmin = require('gulp-cssmin'),
	rewritecss = require('gulp-rewrite-css'),
	sourcemaps = require('gulp-sourcemaps'),
	uglify = require('gulp-uglify'),
	sass = require('gulp-sass');

gulp.task('style', function () {
	var stream = gulp.src('scss/main.scss')
		.pipe(sourcemaps.init())
		.pipe(sass().on('error', sass.logError))
		.pipe(cssmin());

	var dest = 'css';

	return stream
		.pipe(rewritecss({destination: dest}))
		.pipe(autoprefixer('last 10 versions', 'ie 9'))
		.pipe(sourcemaps.write('./', {includeContent: false, sourceRoot: '/scss/'}))
		.pipe(gulp.dest(dest));
});

gulp.task('watch', function () {
	gulp.watch('scss/*.scss', ['style']);
});

gulp.task('default', ['style', 'watch']);
