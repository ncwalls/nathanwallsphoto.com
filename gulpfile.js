var gulp         = require( 'gulp' );
var autoprefixer = require( 'autoprefixer' );
var concat       = require( 'gulp-concat' );
var jshint       = require( 'gulp-jshint' );
var livereload   = require( 'gulp-livereload' );
var notify       = require( 'gulp-notify' );
var postcss      = require( 'gulp-postcss' );
var sass         = require( 'gulp-sass' );
var sourcemaps   = require( 'gulp-sourcemaps' );
var uglify       = require( 'gulp-uglify' );
var watch        = require( 'gulp-watch' );

function logError( error ){
	console.log( error.toString() );
	this.emit( 'end' );
}

gulp.task( 'dbchange', function(){
	return gulp.src( [
			'wp-content/themes/makespace-child/.gulpwatch'
		] )
		.pipe( livereload() );
} );

gulp.task( 'font-awesome', function(){ 
	return gulp.src( 'bower_components/font-awesome/fonts/**.*' ) 
        .pipe( gulp.dest( 'wp-content/themes/makespace-child/fonts' ) ); 
} );

gulp.task( 'lint', function(){
	return gulp.src( [
			'wp-content/themes/makespace-child/src/js/**/*.js',
			'wp-content/themes/makespace-child/src/js/*.js'
		] )
		.pipe( jshint() )
		.pipe( notify( function( file ){
			if( file.jshint.success ){
				return false;
			}
			var errors = file.jshint.results.map( function( data ){
				if( data.error ){
					return 'Line ' + data.error.line + ': ' + data.error.reason;
				}
			} ).join( '\n' );
			return '\n-----------------\n' + file.relative + ' (' + file.jshint.results.length + ' errors)\n-----------------\n' + errors + '\n';
		} ) );
} );

gulp.task( 'php', function(){
	return gulp.src( [
			'wp-content/themes/makespace-child/*.php',
			'wp-content/themes/makespace-child/**/*.php'
		] )
		.pipe( livereload() );
} );

gulp.task( 'sass', function(){
	return gulp.src( 'wp-content/themes/makespace-child/src/scss/style.scss' )
		.pipe( sourcemaps.init() )
		.pipe( sass( {
			outputStyle: 'compressed'
		} ).on( 'error', notify.onError( 'Error: <%= error.message %>' ) ) )
		.pipe( postcss( [ autoprefixer( {
			browsers: [ 'last 2 versions' ],
			cascade: false
		} ) ] ) )
		.pipe( sourcemaps.write( './', {
			includeContent: false,
			sourceRoot: './wp-content/themes/makespace-child/src/scss'
		} ) )
		.pipe( gulp.dest( './wp-content/themes/makespace-child/' ) )
		.pipe( livereload() );
} );

gulp.task( 'scripts', [ 'lint' ], function(){
	return gulp.src( [
			'./bower_components/magnific-popup/dist/jquery.magnific-popup.js',
			'./bower_components/slick-carousel/slick/slick.js',
			'wp-content/themes/makespace-child/src/js/optimized-events.js',
			'wp-content/themes/makespace-child/src/js/framework.js',
			'wp-content/themes/makespace-child/src/js/theme.js'
		] )
		.pipe( sourcemaps.init() )
		.pipe( uglify().on( 'error', notify.onError( 'Error: <%= error.message %>' ) ) )
		.pipe( concat( 'wp-content/themes/makespace-child/scripts.min.js' ).on( 'error', notify.onError( 'Error: <%= error.message %>' ) ) )
		.pipe( gulp.dest( '' ) )
		.pipe( livereload() );
} );

gulp.task( 'watch', function(){
	livereload.listen();
	gulp.watch( [ 'wp-content/themes/makespace-child/.gulpwatch' ], [ 'dbchange' ] );
	gulp.watch( [ 'wp-content/themes/makespace-child/*.php', 'wp-content/themes/makespace-child/**/*.php' ], [ 'php' ] );
	gulp.watch( 'wp-content/themes/makespace-child/src/js/**', [ 'lint', 'scripts' ] );
	gulp.watch( [ 'wp-content/themes/makespace-child/src/scss/**', 'wp-content/themes/makespace-child/src/scss/**/*.scss' ], [ 'sass' ] );
} );

gulp.task( 'default', [
	'sass', 'font-awesome', 'lint', 'scripts', 'watch'
] );