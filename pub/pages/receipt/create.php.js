var file = document.getElementById("receipt.file");
var file_name = document.getElementById("receipt.file_name");
var validationFile = document.querySelector("[data-id='receipt.file']");

file.addEventListener("change", function (e) {
  validationFile.classList.add("hidden");
  var selectedFile = file.files[0];
  file_name.textContent = selectedFile.name;
});