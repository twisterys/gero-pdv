// vite.config.js
import { defineConfig } from "file:///C:/laragon/www/gero/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/laragon/www/gero/node_modules/laravel-vite-plugin/dist/index.js";
import obfuscator from "file:///C:/laragon/www/gero/node_modules/rollup-plugin-obfuscator/dist/rollup-plugin-obfuscator.js";
import react from "file:///C:/laragon/www/gero/node_modules/@vitejs/plugin-react/dist/index.mjs";
var fonts = [
  "resources/fonts/dripicons-v2.eot",
  "resources/fonts/dripicons-v2.svg",
  "resources/fonts/dripicons-v2.ttf",
  "resources/fonts/dripicons-v2.woff",
  "resources/fonts/fa-brands-400.eot",
  "resources/fonts/fa-brands-400.svg",
  "resources/fonts/fa-brands-400.ttf",
  "resources/fonts/fa-brands-400.woff",
  "resources/fonts/fa-brands-400.woff2",
  "resources/fonts/fa-regular-400.eot",
  "resources/fonts/fa-regular-400.svg",
  "resources/fonts/fa-regular-400.ttf",
  "resources/fonts/fa-regular-400.woff",
  "resources/fonts/fa-regular-400.woff2",
  "resources/fonts/fa-solid-900.eot",
  "resources/fonts/fa-solid-900.svg",
  "resources/fonts/fa-solid-900.ttf",
  "resources/fonts/fa-solid-900.woff",
  "resources/fonts/fa-solid-900.woff2",
  "resources/fonts/materialdesignicons-webfont.eot",
  "resources/fonts/materialdesignicons-webfont.ttf",
  "resources/fonts/materialdesignicons-webfont.woff",
  "resources/fonts/materialdesignicons-webfont.woff2",
  "resources/fonts/summernote.eot",
  "resources/fonts/summernote.ttf",
  "resources/fonts/summernote.woff",
  "resources/fonts/themify.eot",
  "resources/fonts/themify.svg",
  "resources/fonts/themify.ttf",
  "resources/fonts/themify.woff",
  "resources/fonts/typicons.css",
  "resources/fonts/typicons.eot",
  "resources/fonts/typicons.min.css",
  "resources/fonts/typicons.svg",
  "resources/fonts/typicons.ttf",
  "resources/fonts/typicons.woff"
];
var base_scss = [
  "resources/css/app-dark.scss",
  "resources/css/app.scss",
  "resources/css/bootstrap-dark.scss",
  "resources/css/bootstrap.scss",
  "resources/css/icons.scss"
];
var js = [
  "resources/js/app.js",
  "resources/js/achats_create.js",
  "resources/js/achats_liste.js",
  "resources/js/article_create.js",
  "resources/js/article_index.js",
  "resources/js/clients_liste.js",
  "resources/js/vente_create.js",
  "resources/js/ventes_liste.js",
  "resources/js/article_modal.js",
  "resources/js/documents_parameters.js"
];
var reactComponents = [
  "resources/js/components/pages/PosParfums.jsx"
];
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        ...fonts,
        ...base_scss,
        ...js,
        ...reactComponents
      ],
      refresh: true
    }),
    react(),
    obfuscator({
      options: {
        compact: true,
        controlFlowFlattening: true,
        controlFlowFlatteningThreshold: 0.75,
        deadCodeInjection: false,
        deadCodeInjectionThreshold: 0.4,
        debugProtection: false,
        debugProtectionInterval: 0,
        disableConsoleOutput: false,
        domainLock: [],
        domainLockRedirectUrl: "about:blank",
        forceTransformStrings: [],
        identifierNamesCache: null,
        identifierNamesGenerator: "mangled-shuffled",
        identifiersDictionary: [],
        identifiersPrefix: "gero",
        ignoreImports: false,
        inputFileName: "",
        log: false,
        numbersToExpressions: false,
        optionsPreset: "medium-obfuscation",
        renameGlobals: false,
        renameProperties: false,
        renamePropertiesMode: "safe",
        reservedNames: [],
        reservedStrings: [],
        seed: 0,
        selfDefending: false,
        simplify: true,
        sourceMap: false,
        sourceMapBaseUrl: "",
        sourceMapFileName: "",
        sourceMapMode: "separate",
        sourceMapSourcesMode: "sources-content",
        splitStrings: false,
        splitStringsChunkLength: 10,
        stringArray: true,
        stringArrayCallsTransform: true,
        stringArrayCallsTransformThreshold: 0.75,
        stringArrayEncoding: ["base64"],
        stringArrayIndexesType: [
          "hexadecimal-number"
        ],
        stringArrayIndexShift: true,
        stringArrayRotate: true,
        stringArrayShuffle: true,
        stringArrayWrappersCount: 1,
        stringArrayWrappersChainedCalls: true,
        stringArrayWrappersParametersMaxCount: 2,
        stringArrayWrappersType: "variable",
        stringArrayThreshold: 0.75,
        target: "browser",
        transformObjectKeys: false,
        unicodeEscapeSequence: false
      }
    })
  ],
  build: {
    commonjsOptions: { transformMixedEsModules: true }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxnZXJvXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxnZXJvXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi9sYXJhZ29uL3d3dy9nZXJvL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcbmltcG9ydCBvYmZ1c2NhdG9yIGZyb20gJ3JvbGx1cC1wbHVnaW4tb2JmdXNjYXRvcic7XG5pbXBvcnQgcmVhY3QgZnJvbSBcIkB2aXRlanMvcGx1Z2luLXJlYWN0XCI7XG5cbmxldCBmb250cyA9IFtcbiAgICAncmVzb3VyY2VzL2ZvbnRzL2RyaXBpY29ucy12Mi5lb3QnLFxuICAgICdyZXNvdXJjZXMvZm9udHMvZHJpcGljb25zLXYyLnN2ZycsXG4gICAgJ3Jlc291cmNlcy9mb250cy9kcmlwaWNvbnMtdjIudHRmJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL2RyaXBpY29ucy12Mi53b2ZmJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL2ZhLWJyYW5kcy00MDAuZW90JyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL2ZhLWJyYW5kcy00MDAuc3ZnJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL2ZhLWJyYW5kcy00MDAudHRmJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL2ZhLWJyYW5kcy00MDAud29mZicsXG4gICAgJ3Jlc291cmNlcy9mb250cy9mYS1icmFuZHMtNDAwLndvZmYyJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL2ZhLXJlZ3VsYXItNDAwLmVvdCcsXG4gICAgJ3Jlc291cmNlcy9mb250cy9mYS1yZWd1bGFyLTQwMC5zdmcnLFxuICAgICdyZXNvdXJjZXMvZm9udHMvZmEtcmVndWxhci00MDAudHRmJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL2ZhLXJlZ3VsYXItNDAwLndvZmYnLFxuICAgICdyZXNvdXJjZXMvZm9udHMvZmEtcmVndWxhci00MDAud29mZjInLFxuICAgICdyZXNvdXJjZXMvZm9udHMvZmEtc29saWQtOTAwLmVvdCcsXG4gICAgJ3Jlc291cmNlcy9mb250cy9mYS1zb2xpZC05MDAuc3ZnJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL2ZhLXNvbGlkLTkwMC50dGYnLFxuICAgICdyZXNvdXJjZXMvZm9udHMvZmEtc29saWQtOTAwLndvZmYnLFxuICAgICdyZXNvdXJjZXMvZm9udHMvZmEtc29saWQtOTAwLndvZmYyJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL21hdGVyaWFsZGVzaWduaWNvbnMtd2ViZm9udC5lb3QnLFxuICAgICdyZXNvdXJjZXMvZm9udHMvbWF0ZXJpYWxkZXNpZ25pY29ucy13ZWJmb250LnR0ZicsXG4gICAgJ3Jlc291cmNlcy9mb250cy9tYXRlcmlhbGRlc2lnbmljb25zLXdlYmZvbnQud29mZicsXG4gICAgJ3Jlc291cmNlcy9mb250cy9tYXRlcmlhbGRlc2lnbmljb25zLXdlYmZvbnQud29mZjInLFxuICAgICdyZXNvdXJjZXMvZm9udHMvc3VtbWVybm90ZS5lb3QnLFxuICAgICdyZXNvdXJjZXMvZm9udHMvc3VtbWVybm90ZS50dGYnLFxuICAgICdyZXNvdXJjZXMvZm9udHMvc3VtbWVybm90ZS53b2ZmJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL3RoZW1pZnkuZW90JyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL3RoZW1pZnkuc3ZnJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL3RoZW1pZnkudHRmJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL3RoZW1pZnkud29mZicsXG4gICAgJ3Jlc291cmNlcy9mb250cy90eXBpY29ucy5jc3MnLFxuICAgICdyZXNvdXJjZXMvZm9udHMvdHlwaWNvbnMuZW90JyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL3R5cGljb25zLm1pbi5jc3MnLFxuICAgICdyZXNvdXJjZXMvZm9udHMvdHlwaWNvbnMuc3ZnJyxcbiAgICAncmVzb3VyY2VzL2ZvbnRzL3R5cGljb25zLnR0ZicsXG4gICAgJ3Jlc291cmNlcy9mb250cy90eXBpY29ucy53b2ZmJyxcbl1cbmxldCBiYXNlX3Njc3MgPSBbXG4gICAgJ3Jlc291cmNlcy9jc3MvYXBwLWRhcmsuc2NzcycsXG4gICAgJ3Jlc291cmNlcy9jc3MvYXBwLnNjc3MnLFxuICAgICdyZXNvdXJjZXMvY3NzL2Jvb3RzdHJhcC1kYXJrLnNjc3MnLFxuICAgICdyZXNvdXJjZXMvY3NzL2Jvb3RzdHJhcC5zY3NzJyxcbiAgICAncmVzb3VyY2VzL2Nzcy9pY29ucy5zY3NzJyxcbl07XG5cbmxldCBqcyA9IFtcbiAgICAncmVzb3VyY2VzL2pzL2FwcC5qcycsXG4gICAgJ3Jlc291cmNlcy9qcy9hY2hhdHNfY3JlYXRlLmpzJyxcbiAgICAncmVzb3VyY2VzL2pzL2FjaGF0c19saXN0ZS5qcycsXG4gICAgJ3Jlc291cmNlcy9qcy9hcnRpY2xlX2NyZWF0ZS5qcycsXG4gICAgJ3Jlc291cmNlcy9qcy9hcnRpY2xlX2luZGV4LmpzJyxcbiAgICAncmVzb3VyY2VzL2pzL2NsaWVudHNfbGlzdGUuanMnLFxuICAgICdyZXNvdXJjZXMvanMvdmVudGVfY3JlYXRlLmpzJyxcbiAgICAncmVzb3VyY2VzL2pzL3ZlbnRlc19saXN0ZS5qcycsXG4gICAgJ3Jlc291cmNlcy9qcy9hcnRpY2xlX21vZGFsLmpzJyxcbiAgICAncmVzb3VyY2VzL2pzL2RvY3VtZW50c19wYXJhbWV0ZXJzLmpzJyxcbl07XG5cbmxldCByZWFjdENvbXBvbmVudHMgPSBbXG4gICAgJ3Jlc291cmNlcy9qcy9jb21wb25lbnRzL3BhZ2VzL1Bvc1BhcmZ1bXMuanN4J1xuXVxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiAgICBwbHVnaW5zOiBbXG4gICAgICAgIGxhcmF2ZWwoe1xuICAgICAgICAgICAgaW5wdXQ6IFtcbiAgICAgICAgICAgICAgICAuLi5mb250cyxcbiAgICAgICAgICAgICAgICAuLi5iYXNlX3Njc3MsXG4gICAgICAgICAgICAgICAgLi4uanMsXG4gICAgICAgICAgICAgICAgLi4ucmVhY3RDb21wb25lbnRzXG4gICAgICAgICAgICBdLFxuICAgICAgICAgICAgcmVmcmVzaDogdHJ1ZSxcbiAgICAgICAgfSksXG4gICAgICAgIHJlYWN0KCksXG4gICAgICAgIG9iZnVzY2F0b3Ioe1xuICAgICAgICAgICAgb3B0aW9uczoge1xuICAgICAgICAgICAgICAgIGNvbXBhY3Q6IHRydWUsXG4gICAgICAgICAgICAgICAgY29udHJvbEZsb3dGbGF0dGVuaW5nOiB0cnVlLFxuICAgICAgICAgICAgICAgIGNvbnRyb2xGbG93RmxhdHRlbmluZ1RocmVzaG9sZDogMC43NSxcbiAgICAgICAgICAgICAgICBkZWFkQ29kZUluamVjdGlvbjogZmFsc2UsXG4gICAgICAgICAgICAgICAgZGVhZENvZGVJbmplY3Rpb25UaHJlc2hvbGQ6IDAuNCxcbiAgICAgICAgICAgICAgICBkZWJ1Z1Byb3RlY3Rpb246IGZhbHNlLFxuICAgICAgICAgICAgICAgIGRlYnVnUHJvdGVjdGlvbkludGVydmFsOiAwLFxuICAgICAgICAgICAgICAgIGRpc2FibGVDb25zb2xlT3V0cHV0OiBmYWxzZSxcbiAgICAgICAgICAgICAgICBkb21haW5Mb2NrOiBbXSxcbiAgICAgICAgICAgICAgICBkb21haW5Mb2NrUmVkaXJlY3RVcmw6ICdhYm91dDpibGFuaycsXG4gICAgICAgICAgICAgICAgZm9yY2VUcmFuc2Zvcm1TdHJpbmdzOiBbXSxcbiAgICAgICAgICAgICAgICBpZGVudGlmaWVyTmFtZXNDYWNoZTogbnVsbCxcbiAgICAgICAgICAgICAgICBpZGVudGlmaWVyTmFtZXNHZW5lcmF0b3I6ICdtYW5nbGVkLXNodWZmbGVkJyxcbiAgICAgICAgICAgICAgICBpZGVudGlmaWVyc0RpY3Rpb25hcnk6IFtdLFxuICAgICAgICAgICAgICAgIGlkZW50aWZpZXJzUHJlZml4OiAnZ2VybycsXG4gICAgICAgICAgICAgICAgaWdub3JlSW1wb3J0czogZmFsc2UsXG4gICAgICAgICAgICAgICAgaW5wdXRGaWxlTmFtZTogJycsXG4gICAgICAgICAgICAgICAgbG9nOiBmYWxzZSxcbiAgICAgICAgICAgICAgICBudW1iZXJzVG9FeHByZXNzaW9uczogZmFsc2UsXG4gICAgICAgICAgICAgICAgb3B0aW9uc1ByZXNldDogJ21lZGl1bS1vYmZ1c2NhdGlvbicsXG4gICAgICAgICAgICAgICAgcmVuYW1lR2xvYmFsczogZmFsc2UsXG4gICAgICAgICAgICAgICAgcmVuYW1lUHJvcGVydGllczogZmFsc2UsXG4gICAgICAgICAgICAgICAgcmVuYW1lUHJvcGVydGllc01vZGU6ICdzYWZlJyxcbiAgICAgICAgICAgICAgICByZXNlcnZlZE5hbWVzOiBbXSxcbiAgICAgICAgICAgICAgICByZXNlcnZlZFN0cmluZ3M6IFtdLFxuICAgICAgICAgICAgICAgIHNlZWQ6IDAsXG4gICAgICAgICAgICAgICAgc2VsZkRlZmVuZGluZzogZmFsc2UsXG4gICAgICAgICAgICAgICAgc2ltcGxpZnk6IHRydWUsXG4gICAgICAgICAgICAgICAgc291cmNlTWFwOiBmYWxzZSxcbiAgICAgICAgICAgICAgICBzb3VyY2VNYXBCYXNlVXJsOiAnJyxcbiAgICAgICAgICAgICAgICBzb3VyY2VNYXBGaWxlTmFtZTogJycsXG4gICAgICAgICAgICAgICAgc291cmNlTWFwTW9kZTogJ3NlcGFyYXRlJyxcbiAgICAgICAgICAgICAgICBzb3VyY2VNYXBTb3VyY2VzTW9kZTogJ3NvdXJjZXMtY29udGVudCcsXG4gICAgICAgICAgICAgICAgc3BsaXRTdHJpbmdzOiBmYWxzZSxcbiAgICAgICAgICAgICAgICBzcGxpdFN0cmluZ3NDaHVua0xlbmd0aDogMTAsXG4gICAgICAgICAgICAgICAgc3RyaW5nQXJyYXk6IHRydWUsXG4gICAgICAgICAgICAgICAgc3RyaW5nQXJyYXlDYWxsc1RyYW5zZm9ybTogdHJ1ZSxcbiAgICAgICAgICAgICAgICBzdHJpbmdBcnJheUNhbGxzVHJhbnNmb3JtVGhyZXNob2xkOiAwLjc1LFxuICAgICAgICAgICAgICAgIHN0cmluZ0FycmF5RW5jb2Rpbmc6IFsnYmFzZTY0J10sXG4gICAgICAgICAgICAgICAgc3RyaW5nQXJyYXlJbmRleGVzVHlwZTogW1xuICAgICAgICAgICAgICAgICAgICAnaGV4YWRlY2ltYWwtbnVtYmVyJ1xuICAgICAgICAgICAgICAgIF0sXG4gICAgICAgICAgICAgICAgc3RyaW5nQXJyYXlJbmRleFNoaWZ0OiB0cnVlLFxuICAgICAgICAgICAgICAgIHN0cmluZ0FycmF5Um90YXRlOiB0cnVlLFxuICAgICAgICAgICAgICAgIHN0cmluZ0FycmF5U2h1ZmZsZTogdHJ1ZSxcbiAgICAgICAgICAgICAgICBzdHJpbmdBcnJheVdyYXBwZXJzQ291bnQ6IDEsXG4gICAgICAgICAgICAgICAgc3RyaW5nQXJyYXlXcmFwcGVyc0NoYWluZWRDYWxsczogdHJ1ZSxcbiAgICAgICAgICAgICAgICBzdHJpbmdBcnJheVdyYXBwZXJzUGFyYW1ldGVyc01heENvdW50OiAyLFxuICAgICAgICAgICAgICAgIHN0cmluZ0FycmF5V3JhcHBlcnNUeXBlOiAndmFyaWFibGUnLFxuICAgICAgICAgICAgICAgIHN0cmluZ0FycmF5VGhyZXNob2xkOiAwLjc1LFxuICAgICAgICAgICAgICAgIHRhcmdldDogJ2Jyb3dzZXInLFxuICAgICAgICAgICAgICAgIHRyYW5zZm9ybU9iamVjdEtleXM6IGZhbHNlLFxuICAgICAgICAgICAgICAgIHVuaWNvZGVFc2NhcGVTZXF1ZW5jZTogZmFsc2VcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH0pLFxuICAgIF0sXG4gICAgYnVpbGQ6e1xuICAgICAgICBjb21tb25qc09wdGlvbnM6IHsgdHJhbnNmb3JtTWl4ZWRFc01vZHVsZXM6IHRydWUgfVxuICAgIH1cbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFtUCxTQUFTLG9CQUFvQjtBQUNoUixPQUFPLGFBQWE7QUFDcEIsT0FBTyxnQkFBZ0I7QUFDdkIsT0FBTyxXQUFXO0FBRWxCLElBQUksUUFBUTtBQUFBLEVBQ1I7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUNKO0FBQ0EsSUFBSSxZQUFZO0FBQUEsRUFDWjtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFDSjtBQUVBLElBQUksS0FBSztBQUFBLEVBQ0w7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLEVBQ0E7QUFDSjtBQUVBLElBQUksa0JBQWtCO0FBQUEsRUFDbEI7QUFDSjtBQUNBLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxRQUNILEdBQUc7QUFBQSxRQUNILEdBQUc7QUFBQSxRQUNILEdBQUc7QUFBQSxRQUNILEdBQUc7QUFBQSxNQUNQO0FBQUEsTUFDQSxTQUFTO0FBQUEsSUFDYixDQUFDO0FBQUEsSUFDRCxNQUFNO0FBQUEsSUFDTixXQUFXO0FBQUEsTUFDUCxTQUFTO0FBQUEsUUFDTCxTQUFTO0FBQUEsUUFDVCx1QkFBdUI7QUFBQSxRQUN2QixnQ0FBZ0M7QUFBQSxRQUNoQyxtQkFBbUI7QUFBQSxRQUNuQiw0QkFBNEI7QUFBQSxRQUM1QixpQkFBaUI7QUFBQSxRQUNqQix5QkFBeUI7QUFBQSxRQUN6QixzQkFBc0I7QUFBQSxRQUN0QixZQUFZLENBQUM7QUFBQSxRQUNiLHVCQUF1QjtBQUFBLFFBQ3ZCLHVCQUF1QixDQUFDO0FBQUEsUUFDeEIsc0JBQXNCO0FBQUEsUUFDdEIsMEJBQTBCO0FBQUEsUUFDMUIsdUJBQXVCLENBQUM7QUFBQSxRQUN4QixtQkFBbUI7QUFBQSxRQUNuQixlQUFlO0FBQUEsUUFDZixlQUFlO0FBQUEsUUFDZixLQUFLO0FBQUEsUUFDTCxzQkFBc0I7QUFBQSxRQUN0QixlQUFlO0FBQUEsUUFDZixlQUFlO0FBQUEsUUFDZixrQkFBa0I7QUFBQSxRQUNsQixzQkFBc0I7QUFBQSxRQUN0QixlQUFlLENBQUM7QUFBQSxRQUNoQixpQkFBaUIsQ0FBQztBQUFBLFFBQ2xCLE1BQU07QUFBQSxRQUNOLGVBQWU7QUFBQSxRQUNmLFVBQVU7QUFBQSxRQUNWLFdBQVc7QUFBQSxRQUNYLGtCQUFrQjtBQUFBLFFBQ2xCLG1CQUFtQjtBQUFBLFFBQ25CLGVBQWU7QUFBQSxRQUNmLHNCQUFzQjtBQUFBLFFBQ3RCLGNBQWM7QUFBQSxRQUNkLHlCQUF5QjtBQUFBLFFBQ3pCLGFBQWE7QUFBQSxRQUNiLDJCQUEyQjtBQUFBLFFBQzNCLG9DQUFvQztBQUFBLFFBQ3BDLHFCQUFxQixDQUFDLFFBQVE7QUFBQSxRQUM5Qix3QkFBd0I7QUFBQSxVQUNwQjtBQUFBLFFBQ0o7QUFBQSxRQUNBLHVCQUF1QjtBQUFBLFFBQ3ZCLG1CQUFtQjtBQUFBLFFBQ25CLG9CQUFvQjtBQUFBLFFBQ3BCLDBCQUEwQjtBQUFBLFFBQzFCLGlDQUFpQztBQUFBLFFBQ2pDLHVDQUF1QztBQUFBLFFBQ3ZDLHlCQUF5QjtBQUFBLFFBQ3pCLHNCQUFzQjtBQUFBLFFBQ3RCLFFBQVE7QUFBQSxRQUNSLHFCQUFxQjtBQUFBLFFBQ3JCLHVCQUF1QjtBQUFBLE1BQzNCO0FBQUEsSUFDSixDQUFDO0FBQUEsRUFDTDtBQUFBLEVBQ0EsT0FBTTtBQUFBLElBQ0YsaUJBQWlCLEVBQUUseUJBQXlCLEtBQUs7QUFBQSxFQUNyRDtBQUNKLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
