function Form(name) {
	var inputs = [];
	var valid = false;
}

Form.prototype.addInput(input) {
	this.inputs[] = input;
}

Form.prototype.isValid() {

}

Form.prototype.getPassword() {

}

function Input(type, validation, form) {
	var type = type;
	var validation = validation;
	var form = form;
	var valid = false;
	var value;
}

Input.prototype.validate() {
	this.value = this.get();
	if(this.type == 'username'){
		if(this.value.length < 3 || this.value.length > 20){
			return false;
		}

		if(!this.value.match(/^[a-zA-Z0-9-_.]*$/)){
			return false;
		}

		return true;
	} else if(this.type == 'email'){
		if(!this.value.match(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)){
			return false;
		}

		return true;
	} else if(this.type == 'password'){
		if(this.value.length < 8 || this.value.length > 72){
			return false;
		}

		return true;
	} else if(this.type == 'pwcheck'){
		if(this.value != this.form.getPassword()){
			return false;
		}

		return true;
	}
}

Input.prototype.get() {
	return document.getElementById(this.form.name + '-' + this.type).value;
}
