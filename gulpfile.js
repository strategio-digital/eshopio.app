/**
 * Copyright (c) 2019 Wakers.cz
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 */

// Balíčky
const
    gulp            = require('gulp'),
    cleanCSS        = require('gulp-clean-css'),
    env             = require('gulp-environment'),
    filesExist      = require('files-exist'),
    concat          = require('gulp-concat'),
    hash            = require('gulp-hash'),
    sass            = require('gulp-sass'),
    uglify          = require('gulp-uglify'),
    packageImporter = require('node-sass-package-importer');

// Asset loadery
const loaders = {
    backend: './assets/static/backend',
    frontend: './assets/static/frontend'
};

// Názvy výsledných souborů (js / scss)
const output = {
    backend: 'backend-build',
    frontend: 'frontend-build',
    manifest: '../manifest.json'
};

// Úložiště výsledných souborů
const storage = {
    JS: './www/temp/static/js',
    CSS: './www/temp/static/css',
    File: './www/temp/static'
};

// Načtené assets
var assets = {
    backend: null,
    frontend: null
};

// Načte assets
gulp.task('load:assets', function (promise) {

    delete require.cache[require.resolve(loaders.backend)];
    delete require.cache[require.resolve(loaders.frontend)];

    assets.backend = require(loaders.backend);
    assets.frontend = require(loaders.frontend);

    promise();
});

// Kompilace JS backendu (custom & system)
gulp.task('compile:backend:js', function (promise) {

    // Případně přidá production only JS
    //assets.frontend.js = (env.is.production() ? assets.frontend.js.concat(assets.frontend.jsOnlyProduction) : assets.frontend.js);

    if (assets.backend.js.length === 0) {
        return promise();
    }

    // Minifikuje, sloučí a uloží název souboru do manifestu
    return gulp.src(filesExist(assets.backend.js))

        .pipe(concat(output.backend + '.js'))
        .pipe(env.if.production(uglify()))

        .pipe(hash())
        .pipe(gulp.dest(storage.JS))
        .pipe(hash.manifest(output.manifest))
        .pipe(gulp.dest(storage.JS));

});

// Kompilace JS frontendu (custom & system)
gulp.task('compile:frontend:js', function (promise) {

    // Případně přidá production only JS
    assets.frontend.js = (env.is.production() ? assets.frontend.js.concat(assets.frontend.jsOnlyProduction) : assets.frontend.js);

    if (assets.frontend.js.length === 0) {
        return promise();
    }

    // Minifikuje, sloučí a uloží název souboru do manifestu
    return gulp.src(filesExist(assets.frontend.js))

        .pipe(concat(output.frontend + '.js'))
        .pipe(env.if.production(uglify()))

        .pipe(hash())
        .pipe(gulp.dest(storage.JS))
        .pipe(hash.manifest(output.manifest))
        .pipe(gulp.dest(storage.JS));

});

// Kompilace SCSS backendu (custom & system)
gulp.task('compile:backend:styles', function (promise) {

    if (assets.backend.scss.length === 0) {
        return promise();
    }

    return gulp.src(filesExist(assets.backend.scss))

        .pipe(env.if
        // compressed / compact
            .production(sass.sync( { importer: packageImporter(), outputStyle: 'compressed' }).on('error', sass.logError))
            .else(sass.sync( { importer: packageImporter(),  outputStyle: 'expanded' }).on('error', sass.logError)))

        .pipe(concat(output.backend  + '.css'))
        .pipe(env.if.production(cleanCSS()))

        .pipe(hash())
        .pipe(gulp.dest(storage.CSS))
        .pipe(hash.manifest(output.manifest))

        .pipe(gulp.dest(storage.CSS));
});

// Kompilace SCSS frontendu
gulp.task('compile:frontend:styles', function (promise) {

    if (assets.frontend.scss.length === 0) {
        return promise();
    }

    return gulp.src(filesExist(assets.frontend.scss))

        .pipe(env.if
            // compressed / compact
            .production(sass.sync( { importer: packageImporter(), outputStyle: 'compressed' }).on('error', sass.logError))
            .else(sass.sync( { importer: packageImporter(),  outputStyle: 'expanded' }).on('error', sass.logError)))

        .pipe(concat(output.frontend  + '.css'))
        .pipe(env.if.production(cleanCSS()))

        .pipe(hash())
        .pipe(gulp.dest(storage.CSS))
        .pipe(hash.manifest(output.manifest))

        .pipe(gulp.dest(storage.CSS));
});

gulp.task('copy:files', function (promise) {

    var files = assets.backend.file
        .concat(assets.frontend.file);

    files.forEach(function (file)
    {
        [].push(gulp.src(filesExist(file.from))
            .pipe(gulp.dest(storage.File + file.to)))
    });

    return promise();
});

// Paralélně zpracuje tasky
gulp.task('default:parallel', gulp.parallel(
    'compile:backend:styles',
    'compile:backend:js',
    'compile:frontend:styles',
    'compile:frontend:js',
    'copy:files'
));

// Watcher
gulp.task('watch', function () {

    gulp.watch([
            'assets/**/*',
            'app/*/assets/**/*'
        ], gulp.series('default')
    );

});

// Výchozí task
gulp.task('default', gulp.series(
    'load:assets',
    'default:parallel'
));
