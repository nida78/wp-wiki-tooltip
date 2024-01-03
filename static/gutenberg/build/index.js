/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/editor.scss":
/*!*************************!*\
  !*** ./src/editor.scss ***!
  \*************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/rich-text":
/*!**********************************!*\
  !*** external ["wp","richText"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["richText"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./editor.scss */ "./src/editor.scss");






const WikiFormatName = 'wp-wiki-tooltip/tooltip-edit';
const WikiFormatClass = 'wiki-tooltip-has-data';
const WikiFormatTag = 'wiki';
let BaseList = [];
BaseList.push({
  value: 'standard',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Standard base', 'wp-wiki-tooltip')
});
BaseList.push({
  value: '',
  label: '---',
  disabled: true
});
for (let elem in wp_wiki_tooltip_mce.wiki_urls.data) {
  if (elem !== '###NEWID###') {
    let value = wp_wiki_tooltip_mce.wiki_urls.data[elem]['id'];
    BaseList.push({
      value: value,
      label: value
    });
  }
}
const WikiTooltipEdit = props => {
  const settings = {
    name: WikiFormatName,
    tagName: WikiFormatTag,
    className: WikiFormatClass
  };
  const {
    contentRef,
    isActive,
    value
  } = props;
  const getAnchor = () => {
    let newAnchor = (0,_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__.useAnchor)({
      editableContentElement: contentRef.current,
      value: value,
      settings: settings
    });
    if (isActive && !(newAnchor instanceof HTMLUnknownElement)) {
      // try to get text selection
      const selection = document.defaultView.getSelection();
      newAnchor = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
    }
    return newAnchor;
  };
  const anchorRef = getAnchor();
  const tooltipValue = {
    title: props.activeAttributes.title === undefined ? '' : props.activeAttributes.title,
    section: props.activeAttributes.section === undefined ? '' : props.activeAttributes.section,
    base: props.activeAttributes.base === undefined || props.activeAttributes.base === '' ? 'standard' : props.activeAttributes.base,
    thumbnail: props.activeAttributes.thumbnail === undefined || props.activeAttributes.thumbnail === '' ? 'standard' : props.activeAttributes.thumbnail
  };
  const onChangeTitle = value => {
    onChangeTooltip('title', value);
  };
  const onChangeSection = value => {
    onChangeTooltip('section', value);
  };
  const onChangeBase = value => {
    onChangeTooltip('base', value);
  };
  const onChangeThumbnail = value => {
    onChangeTooltip('thumbnail', value);
  };
  const onChangeTooltip = (attribute, newValue) => {
    const newTooltipValues = {
      title: '',
      section: '',
      base: '',
      thumbnail: ''
    };

    // read current format data
    if (props.activeAttributes) {
      if (props.activeAttributes.title) {
        newTooltipValues.title = props.activeAttributes.title;
      }
      if (props.activeAttributes.section) {
        newTooltipValues.section = props.activeAttributes.section;
      }
      if (props.activeAttributes.base) {
        newTooltipValues.base = props.activeAttributes.base;
      }
      if (props.activeAttributes.thumbnail) {
        newTooltipValues.thumbnail = props.activeAttributes.thumbnail;
      }
    }

    // update changed values
    if ('title' === attribute) {
      newTooltipValues.title = newValue;
    } else if ('section' === attribute) {
      newTooltipValues.section = newValue;
    } else if ('base' === attribute) {
      newTooltipValues.base = newValue;
    } else if ('thumbnail' === attribute) {
      newTooltipValues.thumbnail = newValue;
    }
    if (newTooltipValues.title === '') {
      delete newTooltipValues.title;
    }
    if (newTooltipValues.section === '') {
      delete newTooltipValues.section;
    }
    if (newTooltipValues.base === '' || newTooltipValues.base === 'standard') {
      delete newTooltipValues.base;
    }
    if (newTooltipValues.thumbnail === '' || newTooltipValues.thumbnail === 'standard') {
      delete newTooltipValues.thumbnail;
    }
    props.onChange((0,_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__.applyFormat)(props.value, {
      type: WikiFormatName,
      attributes: newTooltipValues
    }));
  };
  const onClickToolbarButton = () => {
    props.onChange((0,_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__.toggleFormat)(props.value, {
      type: WikiFormatName
    }));
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, !isActive && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.BlockControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToolbarGroup, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToolbarButton, {
    icon: 'admin-comments',
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('add Wiki Tooltip', 'editor popup', 'wp-wiki-tooltip'),
    onClick: onClickToolbarButton,
    isActive: isActive
  }))), isActive && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.BlockControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToolbarGroup, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToolbarButton, {
    icon: 'welcome-comments',
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('remove Wiki Tooltip', 'editor popup', 'wp-wiki-tooltip'),
    onClick: onClickToolbarButton,
    isActive: isActive
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Popover, {
    headerTitle: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('WP Wiki Tooltip', 'editor popup', 'wp-wiki-tooltip'),
    className: 'wiki-tooltip-data-popover',
    anchor: anchorRef,
    placement: 'bottom-center',
    noArrow: false,
    offset: 5
  }, !(anchorRef instanceof HTMLUnknownElement) && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
    className: 'wiki-tooltip-head'
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('New tooltip has been created. Click element again to modify its settings.', 'editor popup', 'wp-wiki-tooltip'))), anchorRef instanceof HTMLUnknownElement && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
    className: 'wiki-tooltip-head'
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('Change tooltip settings here. Changes are stored immediately.', 'editor popup', 'wp-wiki-tooltip')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('Different Wiki page title', 'editor popup', 'wp-wiki-tooltip'),
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('Enter the title of the requested Wiki page if it differs from the selected text.', 'editor popup', 'wp-wiki-tooltip'),
    className: 'wiki-tooltip-input-title',
    value: tooltipValue.title,
    onChange: onChangeTitle
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('Section title', 'editor popup', 'wp-wiki-tooltip'),
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('Enter the title (anchor) of the requested section in Wiki page.', 'editor popup', 'wp-wiki-tooltip'),
    className: 'wiki-tooltip-input-section',
    value: tooltipValue.section,
    onChange: onChangeSection
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('Wiki base', 'editor popup', 'wp-wiki-tooltip'),
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('Select one of the defined Wiki bases. Visit the settings page to create a new one.', 'editor popup', 'wp-wiki-tooltip'),
    className: 'wiki-tooltip-input-base',
    value: tooltipValue.base,
    onChange: onChangeBase,
    options: BaseList
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('Show thumbnail', 'editor popup', 'wp-wiki-tooltip'),
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('Show a thumbnail in the tooltip?', 'editor popup', 'wp-wiki-tooltip'),
    className: 'wiki-tooltip-input-thumbnail',
    value: tooltipValue.thumbnail,
    onChange: onChangeThumbnail,
    options: [{
      value: 'standard',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('use plugin default value', 'editor popup', 'wp-wiki-tooltip')
    }, {
      value: '',
      label: '---',
      disabled: true
    }, {
      value: 'on',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('yes', 'editor popup', 'wp-wiki-tooltip')
    }, {
      value: 'off',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__._x)('no', 'editor popup', 'wp-wiki-tooltip')
    }]
  })))));
};
(0,_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__.registerFormatType)(WikiFormatName, {
  attributes: {
    title: 'title',
    section: 'section',
    base: 'base',
    thumbnail: 'thumbnail'
  },
  title: 'WP Wiki Tooltip',
  tagName: WikiFormatTag,
  className: WikiFormatClass,
  edit: WikiTooltipEdit
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map