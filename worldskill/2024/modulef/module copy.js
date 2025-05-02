class tab extends HTMLElement{
	constructor(){
		super()
		this.attachShadow({mode:"open"})
		this.shadowRoot.innerHTML=`
			<style>
				:host{
					display:block;
				}
				.tablist{
					display:flex;
					cursor:pointer;
				}
				.tablist div[role=tab]{
					padding:10px 20px;
					border:1px solid #ccc;
					border-bottom:none;
				}
				.tablist div[role=tab][aria-selected=true]{
					background:#eee;
					font-weight:bold;
				}
				.tabpanel{
					border:1px solid #ccc;
					padding:10px;
				}
				[hidden]{
					display:none;
				}
			</style>
			<div class="tablist" role="tablist"></div>
			<div class="tabpanel"></div>
			<slot name="tab" hidden></slot>
			<slot name="panel" hidden></slot>
		`
	}

	connectedCallback(){
		this.tabs=[...this.querySelectorAll("[slot=tab]")]
		this.panels=[...this.querySelectorAll("[slot=panel]")]
		this.tablist=this.shadowRoot.querySelector(".tablist")
		this.tabpanel=this.shadowRoot.querySelector(".tabpanel")

		this.tabs.forEach((tab,i)=>{
			let t=tab.cloneNode(true)
			t.setAttribute("role","tab")
			t.setAttribute("tabindex",i==0?"0":"-1")
			t.setAttribute("aria-selected",i==0?"true":"false")
			t.setAttribute("aria-controls","panel"+(i+1))
			t.addEventListener("click",()=>this.select(i))
			t.addEventListener("keydown",(e)=>{
				if(e.key=="ArrowRight")this.select((i+1)%this.tabs.length)
				if(e.key=="ArrowLeft")this.select((i-1+this.tabs.length)%this.tabs.length)
			})
			this.tablist.appendChild(t)
		})

		this.panels.forEach((panel,i)=>{
			panel.setAttribute("role","tabpanel")
			panel.setAttribute("id","panel"+(i+1))
			panel.setAttribute("aria-labelledby",this.tabs[i].id)
			if(i!=0)panel.setAttribute("aria-hidden","true")
			else panel.setAttribute("aria-hidden","false")
		})

		this.select(0)
	}

	select(index){
		let tabs=this.tablist.querySelectorAll("[role=tab]")
		tabs.forEach((tab,i)=>{
			tab.setAttribute("aria-selected",i==index?"true":"false")
			tab.setAttribute("tabindex",i==index?"0":"-1")
			if(i==index)tab.focus()
		})
		this.panels.forEach((panel,i)=>{
			panel.hidden=i!=index
			panel.setAttribute("aria-hidden",i!=index?"true":"false")
			if(i==index)this.tabpanel.innerHTML=panel.innerHTML
		})
	}
}

customElements.define("info-tab",tab)