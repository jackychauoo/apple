/**
 * Created by vetal on 29.12.15.
 */


var sbLocale = function(tr_arr){
    this._init(tr_arr);
};


jQuery.extend(sbLocale.prototype, {

    translations_arr : [],
    trkeys : [],

    _init : function(tr_arr){
        if(tr_arr) {
            this.translations_arr = tr_arr;
        }
    },

    get : function(key){
        return this.gettext(key);
    },

    gettext : function(key){
        if(typeof this.translations_arr[key] !== 'undefined'){
            return this.translations_arr[key];
        } else {

            //need to save not translated keys in localstorage
            /*
            this.trkeys = _.uniq(_.union(JSON.parse(localStorage.trkeys), this.trkeys));

            if(this.trkeys.indexOf(key) == -1){
                this.trkeys.push(key);
                localStorage.trkeys = JSON.stringify(this.trkeys);
            }
            */

            return key;
        }
    }
});
