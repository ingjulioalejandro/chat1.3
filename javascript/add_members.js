const searchBar = document.querySelector(".search input"),
searchIcon = document.querySelector(".search button"),
usersList = document.querySelector(".users-list");

searchIcon.onclick = ()=>{
  searchBar.classList.toggle("show");
  searchIcon.classList.toggle("active");
  searchBar.focus();
  if(searchBar.classList.contains("active")){
    searchBar.value = "";
    searchBar.classList.remove("active");
  }
}

searchBar.onkeyup = ()=>{
  let searchTerm = searchBar.value;
  if(searchTerm != ""){
    searchBar.classList.add("active");
  }else{
    searchBar.classList.remove("active");
  }
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/search_users_to_add.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          let data = xhr.response;
          usersList.innerHTML = data;
        }
    }
  }
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("searchTerm=" + searchTerm + "&group_id=" + new URLSearchParams(window.location.search).get('group_id'));
}

setInterval(() =>{
  let xhr = new XMLHttpRequest();
  xhr.open("GET", "php/users_to_add.php?group_id=" + new URLSearchParams(window.location.search).get('group_id'), true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          let data = xhr.response;
          if(!searchBar.classList.contains("active")){
            usersList.innerHTML = data;
          }
        } else {
          console.error("Error loading users:", xhr.status, xhr.statusText);
        }
    }
  }
  xhr.onerror = () => {
    console.error("Network error occurred");
  }
  xhr.send();
}, 500);

function addMember(userId) {
  let groupId = new URLSearchParams(window.location.search).get('group_id');
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/add_group_member.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          let response = xhr.response;
          console.log("Server response:", response);
          if(response === "success"){
            alert("Member added successfully");
            location.reload();
          } else {
            alert("Error adding member: " + response);
          }
        }
    }
  }
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("user_id=" + userId + "&group_id=" + groupId);
}
