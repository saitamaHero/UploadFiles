const UPLOAP_MAX_SIZE = 2097152;

const url = 'http://localhost/UploadFile/process.php';
const form = document.querySelector('form');

let result = document.querySelector("#result");

form.addEventListener('submit', e => {
    e.preventDefault();

    const files = document.querySelector('[type=file]').files;
    const formData = new FormData();
    let done = true;

    for (let i = 0; i < files.length; i++) {
        let file = files[i];
        if(file.size < UPLOAP_MAX_SIZE){
            formData.append('files[]', file);
        }else{
            done = false;
            result.innerHTML += createAlert("danger",`<strong>${file.name}</strong> es demasiado grande. Imposible subir`);
            break;
        }
        
    }

    
    
    if(done){
        fetch(url, {
            method: 'POST',
            body: formData,
        }).then(response => response.json())
        .catch(error => {console.log(error);})
        .then(json =>{
            console.log(json);
            
            onDataReceived(json);
        });
    }



    function onDataReceived(json){
        if(json.success){
            result.innerHTML = createAlert("success", "Los archivos han sido subidos correctamente");
        }else{
            json.errs.forEach(error =>{
                result.innerHTML += createAlert("danger",error);
            });
        }
    }


    function createAlert(type, content){
        return `<div class="alert alert-${type}" role="alert">${content}</div>`
    }
});


