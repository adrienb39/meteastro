let selectMenu = document.querySelector("#products");
let category = document.querySelector(".right h2");
let container = document.querySelector(".product-wrapper");

selectMenu.addEventListener("change", function(){
	let categoryName = this.value;
	category.innerHTML = this[this.selectedIndex].text;  

	let http = new XMLHttpRequest();
	http.onreadystatechange = function(){
		if(this.readyState == 4 && this.status == 200){
			let response = JSON.parse(this.responseText);
			// let response = this.responseText;
			let out = "";
			for(let item of response){
				out += `
					<div class="product">
						<div class="left">
							
						</div>
						<div class="right">
							<p class="title">${item.title_contenu}</p>
							<p class="description">${item.contenu}</p>
						</div>
					</div>
				`;
			}
			container.innerHTML = out;
		};
	}	
	http.open('POST', "script.php", true);
	http.setRequestHeader("content-type", "application/x-www-form-urlencoded");
	http.send("category="+categoryName);
});