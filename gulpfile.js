import { src, dest, watch, series, parallel } from "gulp";
import * as dartSass from 'sass'
import gulpSass from "gulp-sass";
import sourcemaps from "gulp-sourcemaps";
import concat from "gulp-concat";
import terser from "gulp-terser-js";
import optimizeImages from "gulp-sharp-optimize-images";

const sass = gulpSass(dartSass)

const paths = {
  scss: "src/scss/**/*.scss",
  js: "src/js/**/*.js",
  images: "src/img/**/*"
}

function css() {
  return src(paths.scss)
    .pipe(sourcemaps.init())
    .pipe(sass())
    .pipe(sourcemaps.write("."))
    .pipe(dest("build/css"))
}

function js() {
  return src(paths.js)
    .pipe(sourcemaps.init())
    .pipe(concat("bundle.js"))
    .pipe(terser())
    .pipe(sourcemaps.write("."))
    .pipe(dest("build/js"))
}

function images() {
  return src(paths.images)
    .pipe(
      optimizeImages({
        webp: {
          quality: 80,
          lossless: false,
          alsoProcessOriginal: true,
        },
        avif: {
          quality: 100,
          lossless: true,
          effort: 4,
        },
        jpg_to_heif: {
          quality: 90,
        },
        png_to_avif: {},

        jpg_to_jpg: {
          quality: 80,
          mozjpeg: true,
        },
      })
    )
    .pipe(dest("build/img"))
}

function watchFiles() {
  watch(paths.scss, css);
  watch(paths.js, js);
  watch(paths.images, images);
}

export { css, watchFiles }
export { images as img }
export { js as bundle }
export default watchFiles