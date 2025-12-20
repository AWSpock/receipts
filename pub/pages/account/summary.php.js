ready(loadData);

async function loadData() {
  var table = document.getElementById("data-table");
  try {
    loader.show(true);

    var response = await fetch(
      "/api/account/" + account_id + "/receipt",
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

      var row = clone.querySelector(".receipt");

      var edit = clone.querySelector("[data-id='edit'] a");
      var edit_link = edit.getAttribute("href");
      edit.setAttribute(
        "href",
        edit_link
          .replace("ACCOUNT_ID", account_id)
          .replace("RECEIPT_ID", i.id)
      );

      var view = clone.querySelector("[data-id='show-preview'] a");
      var view_link = view.getAttribute("href");
      view.setAttribute(
        "href",
        view_link
          .replace("ACCOUNT_ID", account_id)
          .replace("RECEIPT_ID", i.id)
      );

      clone.querySelector(
        '[data-id="store"]'
      ).textContent = i.store;
      clone.querySelector(
        '[data-id="date"]'
      ).textContent = i.date;
      clone.querySelector(
        '[data-id="amount"]'
      ).textContent = i.amount;
      clone.querySelector(
        '[data-id="size"]'
      ).textContent = i.file_size;
      clone.querySelector(
        '[data-id="type"]'
      ).textContent = i.file_type;
      // clone.querySelector(
      //   '[data-id="created"] .data-table-cell-content'
      // ).textContent = i.created;
      // clone.querySelector(
      //   '[data-id="updated"] .data-table-cell-content'
      // ).textContent = i.updated;

      table.appendChild(clone);

      x++;
    });

    convertAllFields();
  } catch (error) {
    console.error(error);
    table.innerHTML = "<div class='alert alert-danger'>" + error + "</div>";
  }
}

// if (navigator.userAgentData) {
//   // if (navigator.userAgentData.mobile) {
//     document.querySelectorAll("[data-id='preview']").forEach(function (el) {
//       el.remove();
//     });
//   // } else {
//   //   document
//   //     .querySelectorAll("[data-id='show-preview']")
//   //     .forEach(function (el) {
//   //       el.remove();
//   //     });
//   // }
// }

// async function initMap() {
//   const response = await fetch("/api/account/" + account_id + "/location");
//   const locations = await response.json();

//   // Create map (temporary center, will be replaced by fitBounds)
//   const map = L.map("map").setView([0, 0], 2);

//   // Load free OSM tiles
//   L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
//     maxZoom: 19,
//     attribution: "© OpenStreetMap contributors",
//   }).addTo(map);

//   // Create bounds object
//   const bounds = L.latLngBounds();

//   // Add markers
//   locations.forEach((loc) => {
//     const marker = L.marker([loc.latitude, loc.longitude]).addTo(map);

//     // extend bounds for each marker
//     bounds.extend([loc.latitude, loc.longitude]);

//     marker.bindPopup(`
//             <strong>${loc.name}</strong><br>${loc.latitude}, ${loc.longitude}
//         `);
//   });

//   // Fit map to bounds
//   if (locations.length > 0) {
//     map.fitBounds(bounds, {
//       padding: [40, 40], // optional padding
//     });
//   }
// }
// // initMap();
