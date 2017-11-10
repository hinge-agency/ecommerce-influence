var gulp  = require('gulp');
var del = require('del');
var zip = require('gulp-zip');
var sass = require('gulp-sass');
var shell = require('gulp-shell');
var args   = require('yargs').argv;
var composer = require('gulp-composer');
var runSequence = require('run-sequence');
var os = require('os');
var homeDir = os.homedir();
var releaseBase = homeDir + '/projects/releases/leadpages-wordpress-v2/';
var releaseFolder = releaseBase+'leadpages';
/********
 sass
 *******/

gulp.task('sass', function () {
    return gulp.src('./App/assets/sass/styles.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./App/assets/css'));
});

gulp.task('sass:watch', function () {
    gulp.watch('./App/assets/sass/*.scss', ['sass']);
});

//remove files form release dir so all new files can be rebuilt
gulp.task('removeallfiles',function(){
    return del([releaseFolder+'/**/*'], {force: true});
});

gulp.task('runcomposer', function(){
    return composer("update --no-dev");
});

gulp.task('run_composer_release', function(){
    return composer("update --no-dev --working-dir " + releaseFolder);
});

gulp.task('movetoreleases', function(){

    return gulp.src(['**/*'], {"base" : "."})
        .pipe(gulp.dest(releaseFolder));
});

gulp.task('removeUnneedFiles',function(){
    return del([
        releaseFolder+'/node_modules',
        releaseFolder+'/tests',
        releaseFolder+'/bin',
        releaseFolder+'/vendor/bin/phantomjs',
        releaseFolder+'/vendor/jakoch',
    ], {force: true});
});

gulp.task('runcomposer2', function(){
    return composer("update");
});

gulp.task('compressrelease', function(){
    return gulp.src(releaseFolder)
        .pipe(zip('leadpagesv2.zip'))
        .pipe(gulp.dest(releaseBase));
});

gulp.task('deploy', function(){
    runSequence(
        //'compressandmove',
        'removeallfiles',
        'runcomposer',
        'movetoreleases',
        'removeUnneedFiles',
        'runcomposer2'
    );
});


/******
 * Unit test setup
 */

var testingFolder ='/Applications/MAMP/htdocs/wordpress_unit_test/wp-content/plugins/leadpages';
var enviroment = args.env;

gulp.task('move_to_test', function(){
    return gulp.src(['**/*'], {"base" : "."})
        .pipe(gulp.dest(testingFolder));
});

gulp.task('run_composer_tests', function(){
    return composer("update --working-dir /Applications/MAMP/htdocs/wordpress_unit_test/wp-content/plugins/leadpages");
});

gulp.task('run_integration_tests', shell.task([
    "php vendor/bin/wpcept run integration --env "+ enviroment
]));

gulp.task('accept_test', shell.task([
    "php vendor/bin/codecept run acceptance --env "+ enviroment
]));

gulp.task('setup_unit_test_plugin', function(){
    runSequence(
        'move_to_test',
        'run_composer_tests'
    );
});


