var file = document.getElementById("receipt.file");
var file_name = document.getElementById("receipt.file_name");

file.addEventListener("change", function (e) {
  var selectedFile = file.files[0];
  file_name.textContent = selectedFile.name;
});

if (navigator.userAgentData) {
  if (navigator.userAgentData.mobile) {
    document.querySelectorAll("[data-id='preview']").forEach(function (el) {
      el.remove();
    });
  } else {
    document
      .querySelectorAll("[data-id='show-preview']")
      .forEach(function (el) {
        el.remove();
      });
  }
}
