function searchProduct() {
    let query = document.getElementById("search").value.trim();

    // If search input is empty, reload all products
    if (query === "") {
       fetch("search.php?query=")
          .then(response => response.text())
          .then(data => {
             document.getElementById("product-list").innerHTML = data;
          })
          .catch(error => console.error("Error fetching data:", error));
       return;
    }

    // Fetch search results
    fetch("search.php?query=" + encodeURIComponent(query))
       .then(response => response.text())
       .then(data => {
          // If no products found, show a message
          if (data.includes("No products found")) {
             document.getElementById("product-list").innerHTML = "<tr><td colspan='9' style='text-align: center;'>No products found</td></tr>";
          } else {
             document.getElementById("product-list").innerHTML = data;
          }
       })
       .catch(error => console.error("Error fetching data:", error));
 }


