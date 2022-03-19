import Masonry from 'masonry-layout';

var masonry = new Masonry('.imageResults',{
    itemSelector:'.grid-item',
    columnWidth:200,
    fitWidth:true
    
})

setTimeout(function(){masonry.layout()},500)