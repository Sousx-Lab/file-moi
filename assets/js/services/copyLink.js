
export default function copyDownloadLink(mutationsList){

    mutationsList.forEach(function(mutation){
        let copyButton = mutation.target.querySelectorAll('button.copy');
        copyButton.forEach(function(elem){
            elem.addEventListener('click', function(e){
                console.log(e.target);
                navigator.clipboard.writeText(e.target.dataset.link);
                e.target.innerText = "Copy link 📋"
                setTimeout(function () {
                    e.target.innerText = "Copy link";
                }, 1000)
            })
        })
    })
    
}
