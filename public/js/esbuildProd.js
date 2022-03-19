
require('esbuild').build({
    entryPoints: ['masonry.js','./src/validate','./src/index.js'],
    outdir:"dist",
    allowOverwrite: true,
    bundle: true,
    sourcemap: true,
    sourcesContent: false,
    minify:true
    
  })