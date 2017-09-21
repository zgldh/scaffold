import Vue from 'vue';
import VueI18n from 'vue-i18n';
import enLocale from 'element-ui/lib/locale/lang/en';
import zhLocale from 'element-ui/lib/locale/lang/zh-CN';
Vue.use(VueI18n);

export const i18n = new VueI18n({
  locale: window.Laravel.Locale, // set locale
});
i18n.mergeLocaleMessage(window.Laravel.Locale, window.Laravel.Languages);
i18n.mergeLocaleMessage('zh-CN', zhLocale);
i18n.mergeLocaleMessage('en', enLocale);

export function loadModuleLanguage (languageModule) {
  return {
    beforeRouteEnter: function (to, from, next) {
      loadLanguages(languageModule).then(next);
    }
  }
};

var loadingPromises = {};

export function loadLanguages (languageModule) {
  var message = i18n.getLocaleMessage(i18n.locale);
  if (!message || !message.hasOwnProperty(languageModule)) {
    // Gonna to load language
    if (!loadingPromises.hasOwnProperty(languageModule)) {
      loadingPromises[languageModule] = axios.get('/lang/' + languageModule).then(result => {
        var langs = {};
        langs[languageModule] = result.data;
        i18n.mergeLocaleMessage(i18n.locale, langs);
        loadingPromises[languageModule] = null;
      });
    }
    return loadingPromises[languageModule];
  }
  return Promise.resolve();
};
