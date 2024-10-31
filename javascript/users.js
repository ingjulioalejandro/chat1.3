document.addEventListener('DOMContentLoaded', function() {
  const searchBar = document.querySelector(".search input"),
        searchIcon = document.querySelector(".search button"),
        usersList = document.querySelector(".users-list");

  if (searchIcon) {
      searchIcon.onclick = ()=>{
          searchBar.classList.toggle("show");
          searchIcon.classList.toggle("active");
          searchBar.focus();
          if(searchBar.classList.contains("active")){
              searchBar.value = "";
              searchBar.classList.remove("active");
          }
      }
  }

  if (searchBar) {
      searchBar.onkeyup = ()=>{
          let searchTerm = searchBar.value;
          if(searchTerm != ""){
              searchBar.classList.add("active");
          }else{
              searchBar.classList.remove("active");
          }
          let xhr = new XMLHttpRequest();
          xhr.open("POST", "php/search.php", true);
          xhr.onload = ()=>{
              if(xhr.readyState === XMLHttpRequest.DONE){
                  if(xhr.status === 200 && usersList){
                      usersList.innerHTML = xhr.response;
                  }
              }
          }
          xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhr.send("searchTerm=" + searchTerm);
      }
  }

  function updateUsersList() {
      if (!usersList) return; // Si no existe usersList, salir

      let xhr = new XMLHttpRequest();
      xhr.open("GET", "php/users.php", true);
      xhr.onload = ()=>{
          if(xhr.readyState === XMLHttpRequest.DONE){
              if(xhr.status === 200){
                  if(usersList && !searchBar.classList.contains("active")){
                      usersList.innerHTML = xhr.response;
                  }
              }
          }
      }
      xhr.send();
  }

  // Primera actualización
  updateUsersList();
  
  // Actualizar cada 500ms
  const interval = setInterval(() => {
      if (document.querySelector(".users-list")) {
          updateUsersList();
      } else {
          clearInterval(interval); // Detener si el elemento no existe
      }
  }, 500);
});