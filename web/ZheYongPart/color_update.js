// Called when "Add Another Color" is clicked
function addColorInput() {
    const colorInputsDiv = document.getElementById("color-inputs");
    
    const newColorInput = document.createElement("div");
    newColorInput.classList.add("color-input");

    newColorInput.innerHTML = `
        <input type="text" name="color_names[]" placeholder="Enter color name (e.g., Red)" class="box">
        <input type="file" name="color_images[]" accept="image/*" class="box">
        <button type="button" class="remove-color-btn" onclick="removeColorInput(this)">Remove</button>
    `;

    colorInputsDiv.appendChild(newColorInput);
}

// Called when a "Remove" button is clicked
function removeColorInput(button) {
    const inputDiv = button.parentElement;
    inputDiv.remove();
}



