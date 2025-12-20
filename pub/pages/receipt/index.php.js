ready(loadData);

async function loadData() {
  var table = document.getElementById("data-table");
  try {
    loader.show(true);

    var response = await fetch(
      "/api/account/" + account_id + "/document",
      {
        method: "GET",
      }
    );
    // console.log(response);

    if (!response.ok) {
      throw new Error(`${response.statusText}`);
    }

    var data = await response.json();
    // console.log(data);

    loader.hide();

    var template = document.getElementById("template");
    // console.log(template);

    document.getElementById("data-table-count").textContent =
      data.length.toLocaleString("en-US");

    var x = 0;
    data.forEach(function (i) {
      var clone = template.content.cloneNode(true);

      var row = clone.querySelector(".data-table-row");
      var edit_link = row.getAttribute("href");
      row.setAttribute(
        "href",
        edit_link
          .replace("ACCOUNT_ID", account_id)
          .replace("RECEIPT_ID", i.id)
      );

      clone.querySelector(
        '[data-id="name"] .data-table-cell-content'
      ).textContent = i.name;
      clone.querySelector(
        '[data-id="description"] .data-table-cell-content'
      ).textContent = i.description;
      // clone.querySelector(
      //   '[data-id="created"] .data-table-cell-content'
      // ).textContent = i.created;
      clone.querySelector(
        '[data-id="updated"] .data-table-cell-content'
      ).textContent = i.updated;

      table.appendChild(clone);

      x++;
    });

    convertAllFields();
  } catch (error) {
    console.error(error);
    table.innerHTML = "<div class='alert alert-danger'>" + error + "</div>";
  }
}