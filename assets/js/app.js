import * as Turbo from '@hotwired/turbo';
import '@grafikart/drop-files-element';
import copyDownloadLink  from './services/copyLink';
import handleForm from './services/form';

Turbo.start();
['turbo:load', 'turbo:render'].forEach(e => {
    document.addEventListener(e, function(){
        handleForm();
    })
});

document.addEventListener('turbo:before-stream-render', function(e){
    let target = document.getElementById('download')
    const config = { attributes: true, childList: true, characterData: true};

    const observer = new MutationObserver(copyDownloadLink);
    observer.observe(target, config);
})