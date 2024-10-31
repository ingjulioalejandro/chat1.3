const form = document.querySelector(".typing-area"),
incoming_id = form.querySelector(".incoming_id").value,
inputField = form.querySelector(".input-field"),
sendBtn = form.querySelector("button"),
chatBox = document.querySelector(".chat-box"),
attachmentInput = form.querySelector("#attachment");


let attachmentPreview = document.querySelector(".attachment-preview");
if (!attachmentPreview) {
    attachmentPreview = document.createElement("div");
    attachmentPreview.className = "attachment-preview";
    form.insertBefore(attachmentPreview, sendBtn);
}

form.onsubmit = (e)=>{
    e.preventDefault();
}

inputField.focus();
inputField.onkeyup = ()=>{
    if(inputField.value != "" || attachmentInput.files.length > 0){
        sendBtn.classList.add("active");
    }else{
        sendBtn.classList.remove("active");
    }
}

sendBtn.onclick = ()=>{
    let formData = new FormData(form);
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/insert-chat.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              console.log("Respuesta del servidor:", xhr.responseText);
              inputField.value = "";
              attachmentInput.value = "";
              attachmentPreview.innerHTML = "";
              scrollToBottom();
          } else {
              console.error("Error en la respuesta del servidor:", xhr.status, xhr.statusText);
          }
      }
    }
    xhr.onerror = (e) => {
        console.error("Error de red:", e);
    }
    xhr.send(formData);
}

attachmentInput.onchange = ()=>{
    const file = attachmentInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let fileIcon = getFileIcon(file.name.split('.').pop().toLowerCase());
            attachmentPreview.innerHTML = `
                <div class="attachment-item">
                    <i class="${fileIcon}"></i>
                    <span>${file.name}</span>
                    <button type="button" class="remove-attachment">&times;</button>
                </div>
            `;
            const removeBtn = attachmentPreview.querySelector(".remove-attachment");
            removeBtn.onclick = ()=>{
                attachmentInput.value = "";
                attachmentPreview.innerHTML = "";
                if(inputField.value == ""){
                    sendBtn.classList.remove("active");
                }
            }
        }
        reader.readAsDataURL(file);
    }
    if(inputField.value != "" || attachmentInput.files.length > 0){
        sendBtn.classList.add("active");
    }
}

function getFileIcon(extension) {
    const iconClasses = {
        'pdf': 'far fa-file-pdf',
        'doc': 'far fa-file-word',
        'docx': 'far fa-file-word',
        'xls': 'far fa-file-excel',
        'xlsx': 'far fa-file-excel',
        'txt': 'far fa-file-alt',
        'csv': 'far fa-file-csv',
        'zip': 'far fa-file-archive',
        'rar': 'far fa-file-archive',
        'mp3': 'far fa-file-audio',
        'mp4': 'far fa-file-video',
        'avi': 'far fa-file-video',
        'mov': 'far fa-file-video',
        'jpg': 'far fa-file-image',
        'jpeg': 'far fa-file-image',
        'png': 'far fa-file-image',
        'gif': 'far fa-file-image'
    };

    return iconClasses[extension] || 'far fa-file';
}

chatBox.onmouseenter = ()=>{
    chatBox.classList.add("active");
}

chatBox.onmouseleave = ()=>{
    chatBox.classList.remove("active");
}

setInterval(() =>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get-chat.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
            let data = xhr.response;
            chatBox.innerHTML = data;
            if(!chatBox.classList.contains("active")){
                scrollToBottom();
              }
          }
      }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("incoming_id="+incoming_id);
}, 500);

function scrollToBottom(){
    chatBox.scrollTop = chatBox.scrollHeight;
}
