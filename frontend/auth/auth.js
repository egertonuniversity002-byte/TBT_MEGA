const loader = document.getElementById("loader");
const msgBox = document.getElementById("messageBox");
// Centralized API base (override by defining window.API_BASE earlier if needed)
const API_BASE = window.API_BASE || "https://official-paypal.onrender.com";

// Show styled messages
function showMessage(text, type) {
  msgBox.innerText = text;
  msgBox.className = "message " + type;
  msgBox.style.display = "block";
  setTimeout(() => msgBox.style.display = "none", 4000);
}

// Handle form submission
async function handleForm(formId, endpoint, successRedirect, loadingText) {
  const form = document.getElementById(formId);
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    loader.style.display = "flex";
    loader.querySelector("p").innerText = loadingText;

    const formData = {};
    new FormData(form).forEach((value, key) => formData[key] = value);

    try {
      const res = await fetch(endpoint.startsWith('http') ? endpoint : `${API_BASE}${endpoint}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData)
      });

      const result = await res.json();
      loader.style.display = "none";

      if (result.status === "success") {
        showMessage("✅ " + result.message, "success");
        if (successRedirect) {
          setTimeout(() => window.location.href = successRedirect, 1500);
        }
      } else {
        showMessage("❌ " + (result.message || "Something went wrong"), "error");
      }
    } catch (err) {
      loader.style.display = "none";
      showMessage("⚠️ Network error. Please try again.", "error");
    }
  });
}
