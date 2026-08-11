/* ==========================================================================
   登入頁
   對應 API 1（會員登入）
   ========================================================================== */

renderHeader("signin");

// 已登入時直接回到首頁
if (Session.user()) {
	location.href = "index.html";
}

document.getElementById("signin-form").addEventListener("submit", async (event) => {
	event.preventDefault();

	const messageElement = document.getElementById("message");
	const submitButton = document.getElementById("submit");

	hideMessage(messageElement);
	submitButton.disabled = true;

	try {
		const user = await api("POST", "/user/login", {
			auth: false,
			json: {
				email: document.getElementById("email").value,
				password: document.getElementById("password").value,
			},
		});

		Session.save(user);
		location.href = "index.html";
	} catch (error) {
		showMessage(messageElement, error.message);
	} finally {
		submitButton.disabled = false;
	}
});
