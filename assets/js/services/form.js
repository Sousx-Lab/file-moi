export default function handleForm(){
    let submitButton = document.querySelector("button[type=submit]")
    submitButton.disabled = true;
    
    let files = document.getElementById('files');
    files.addEventListener('change', function(e){
        files.files.length > 0 ? submitButton.disabled = false : submitButton.disabled = true;
    })
}