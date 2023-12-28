const App = (function () {
  "use strict";

  const validate = (event, form) => {
    let errors = false;
    if (form.checkValidity() === false) {
      errors = true;
      //event.preventDefault();
      //event.stopPropagation();
    }
    form.classList.add("was-validated");
    return !errors && (!event.detail || event.detail === 1);
  };

  PointerEvent.prototype.validate = function () {
    return validate(this.target, this.target.closest("form"));
  };

  PointerEvent.prototype.loader = function () {
    const loadingText = this.target.dataset.loadText;
    const initialState = this.target.innerHTML;
    const self = this.target;
    return {
      start: () => {
        self.disabled = true;
        self.innerHTML =
          loadingText ||
          '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Aguarde';
      },
      reset: () => {
        self.innerHTML = initialState;
        self.disabled = false;
      },
    };
  };

  return {
    storage: {
      deseralize: (value) => {
        return JSON.parse(value);
      },
      set: (value) => {
        localStorage.setItem("formstep", value);
      },
      get: () => {
        const dd = App.storage.deseralize(localStorage.getItem("formstep"));
        if (dd && dd.length > 0) {
          return dd[dd.length - 1];
        }
        return null;
      },
      clear: () => {
        localStorage.removeItem("formstep");
      },
      append: (value) => {
        //let current = App.storage.get() || [];
        let current = [];
        if(App.storage.get() != null)
          current = App.storage.get();
        
        current.push(value);
        App.storage.set(JSON.stringify(current));
      },
    },
  };
})();
