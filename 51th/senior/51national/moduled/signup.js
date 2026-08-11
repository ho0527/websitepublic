/* ==========================================================================
   註冊頁
   對應 API 3（會員註冊），註冊成功後自動登入
   ========================================================================== */

renderHeader("signup");

// 已登入時直接回到首頁
if (Session.user()) {
	location.href = "index.html";
}

document.getElementById("signup-form").addEventListener("submit", async (event) => {
	event.preventDefault();

	const messageElement = document.getElementById("message");
	const submitButton = document.getElementById("submit");
	const email = document.getElementById("email").value;
	const password = document.getElementById("password").value;

	hideMessage(messageElement);
	submitButton.disabled = true;

	try {
		await api("POST", "/user/register", {
			auth: false,
			json: {
				email: email,
				password: password,
				nickname: document.getElementById("nickname").value,
			},
		});

		// 註冊成功後直接登入，讓使用者可以立刻開始刊登房屋
		const user = await api("POST", "/user/login", {
			auth: false,
			json: { email: email, password: password },
		});

		Session.save(user);
		location.href = "index.html";
	} catch (error) {
		showMessage(messageElement, error.message);
	} finally {
		submitButton.disabled = false;
	}
});
