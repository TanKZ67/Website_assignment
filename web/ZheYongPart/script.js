document.addEventListener("DOMContentLoaded", function () {
    const dropArea = document.getElementById("drop-area");
    const inputFile = document.getElementById("input-file");
    const imageView = document.getElementById("img-view");

    // Allow click to open file picker
    dropArea.addEventListener("click", function () {
        inputFile.click();
    });

    // Function to update image preview
    function uploadImage(file) {
        let imgLink = URL.createObjectURL(file);
        imageView.style.backgroundImage = `url(${imgLink})`;
        imageView.style.backgroundSize = "cover";
        imageView.style.backgroundPosition = "center";
        imageView.style.backgroundRepeat = "no-repeat";
        imageView.innerHTML = ""; // Clear any existing content
    }

    inputFile.addEventListener("change", function () {
        if (inputFile.files.length > 0) {
            uploadImage(inputFile.files[0]);
        }
    });

    dropArea.addEventListener("dragover", function (e) {
        e.preventDefault();
        dropArea.style.border = "2px dashed #007bff";
    });

    dropArea.addEventListener("dragleave", function (e) {
        e.preventDefault();
        dropArea.style.border = "2px dashed #ccc";
    });

    dropArea.addEventListener("drop", function (e) {
        e.preventDefault();
        dropArea.style.border = "2px dashed #ccc";

        let files = e.dataTransfer.files;
        if (files.length > 0) {
            let dataTransfer = new DataTransfer();
            dataTransfer.items.add(files[0]);
            inputFile.files = dataTransfer.files;

            uploadImage(files[0]);
        }
    });
});
