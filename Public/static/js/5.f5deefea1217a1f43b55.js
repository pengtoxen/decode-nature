webpackJsonp([5],{OvRC:function(t,e,a){t.exports={default:a("oM7Q"),__esModule:!0}},TEOP:function(t,e,a){var i=a("VJF5");"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);a("rjj0")("5e10305c",i,!0)},VJF5:function(t,e,a){(t.exports=a("FZ+f")(!1)).push([t.i,"\n.filter-container input{\n  height: 2.5em !important;\n}\n#att li:hover{\n  cursor: pointer;\n}\n.list-table-expand {\n  font-size: 0;\n}\n.list-table-expand label {\n  width: 90px;\n  color: #99a9bf;\n}\n.list-table-expand .el-form-item {\n  margin-right: 0;\n  margin-bottom: 0;\n  width: 50%;\n}\ntd.el-table__expanded-cell{\n  padding-left: 2em !important;\n}\n#detail .title{\n  font-size: 1em;\n}\n#detail .el-collapse-item__header{\n  font-size: 1.15em;\n}\n#detail .el-tabs__item{\n  font-size: 1.15em;\n}\n#detail .el-col {\n  border-radius: 0px;\n}\n#detail .row-css {\n  border-bottom: 1px rgba(81, 177, 206, 0.5) dashed;\n}\n/* .col-content {\n  border-bottom: 1px gray solid;\n} */\n#detail .grid-content {\n  min-height: 20px;\n}\n#detail .row-bg {\n  padding: 10px 0;\n  background-color: #f9fafc;\n}\n#detail .el-dialog__body {\n  padding-top: 0px;\n}\n",""])},ctMr:function(t,e,a){var i=a("z4F4");"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);a("rjj0")("6077c87a",i,!0)},gN2T:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=a("OvRC"),s=a.n(i),l=a("svVh"),n=a("woOf"),o=a.n(n),r=(a("ctMr"),{bind:function(t,e){t.addEventListener("click",function(a){var i=o()({},e.value),s=o()({ele:t,type:"hit",color:"rgba(0, 0, 0, 0.15)"},i),l=s.ele;if(l){l.style.position="relative",l.style.overflow="hidden";var n=l.getBoundingClientRect(),r=l.querySelector(".waves-ripple");switch(r?r.className="waves-ripple":((r=document.createElement("span")).className="waves-ripple",r.style.height=r.style.width=Math.max(n.width,n.height)+"px",l.appendChild(r)),s.type){case"center":r.style.top=n.height/2-r.offsetHeight/2+"px",r.style.left=n.width/2-r.offsetWidth/2+"px";break;default:r.style.top=a.pageY-n.top-r.offsetHeight/2-document.body.scrollTop+"px",r.style.left=a.pageX-n.left-r.offsetWidth/2-document.body.scrollLeft+"px"}return r.style.backgroundColor=s.color,r.className="waves-ripple z-active",!1}},!1)}}),c=function(t){t.directive("waves",r)};window.Vue&&(window.waves=r,Vue.use(c)),r.install=c;var d=r,p=a("0xDb"),u=a("uFqY"),_=a("Fs0j"),m={name:"fossil",components:{Classification:u.a,GeoData:_.a},directives:{waves:d},data:function(){return{dialogImageUrl:"",dialogVisible:!1,activeNames:["passport"],photoTabs:"photo",clfClass:"filter-item",clfStyle:"width: 150px",clfClear:!0,geoClass:"filter-item",geoStyle:"width: 150px",geoClear:!0,tableKey:0,list:null,total:null,listLoading:!0,listQuery:{page:1,limit:10,name_zh:"",classification:"",geo_age:""},textMap:{update:"Edit",create:"Create"},downloadLoading:!1,createRoute:"/specimen/fossil/create",editRoute:{path:"/specimen/fossil/edit",query:{id:"0"}},baseApi:"https://api-prod",dialogDetailVisible:!1,detail:{id:void 0,status:1,is_comment:!1,serial_no:"",name_zh:"",name_en:"",owner:"",get_time:"",classification_name:"",district_name:"",address:"",geo_age_name:"",geo_layer:"",longitude:"",latitude:"",altitude:"",abstract:"",description:"",photo:[],restore_photo:[],storage_name:"",storage_no:"",num:1,material:[]}}},filters:{isPublicType:function(t){return{1:"success",0:"info"}[t]},isPublic:function(t){return{1:"public",0:"private"}[t]}},created:function(){this.$peng.bindScope(this),this.getList()},methods:{getList:function(){var t=this;this.listLoading=!0,Object(l.d)(this.listQuery).then(function(e){t.list=e.data.data.lists,t.total=parseInt(e.data.data.total),t.listLoading=!1})},handleFilter:function(){this.getList()},handleSizeChange:function(t){this.listQuery.limit=t,this.getList()},handleCurrentChange:function(t){this.listQuery.page=t,this.getList()},handleSwitchPublic:function(t,e){var a=this,i={id:t.id,field:"is_public",value:e};Object(l.e)(i).then(function(i){0===i.data.code?(t.is_public=e,a.$peng.msgOk(a.$t("common.message.operate_success"))):a.$peng.msgInf(a.$t("common.message.operate_fail"))}).catch(function(){a.$peng.msgInf(a.$t("common.message.operate_fail"))})},handleCreate:function(){this.$router.push(this.createRoute)},handleUpdate:function(t){this.editRoute.query.id=t.id,this.$router.push(this.editRoute)},handleDelete:function(t){var e=this;this.$confirm("此操作将永久删除该数据, 是否继续?",this.$t("common.message.info"),{confirmButtonText:this.$t("common.message.confirm"),cancelButtonText:this.$t("common.message.cancel"),type:"warning"}).then(function(){var a={id:t.id};Object(l.b)(a).then(function(a){if(0===a.data.code){e.$peng.msgOk(e.$t("common.message.operate_success"));var i=e.list.indexOf(t);e.list.splice(i,1)}else e.$peng.msgOk(e.$t("common.message.operate_fail"))})}).catch(function(){})},handleDownload:function(){var t=this;this.downloadLoading=!0,Promise.all([a.e(20),a.e(19)]).then(a.bind(null,"zWO4")).then(function(e){var a=t.formatJson(["timestamp","title","type","importance","status"],t.list);e.export_json_to_excel(["timestamp","title","type","importance","status"],a,"table-list"),t.downloadLoading=!1})},formatJson:function(t,e){return e.map(function(e){return t.map(function(t){return"timestamp"===t?Object(p.b)(e[t]):e[t]})})},bindGeoData:function(t){this.listQuery.geo_age=t},bindClfData:function(t){this.listQuery.classification=t},clearClfData:function(t){this.listQuery.classification=t},clearGeoData:function(t){this.listQuery.geo_age=t},handleDetail:function(t){if(this.activeNames=["passport"],this.photoTabs="photo",this.dialogDetailVisible=!0,this.detail=s()(t),this.detail.photo&&"string"==typeof this.detail.photo){var e=this.baseApi;this.detail.photo=JSON.parse(this.detail.photo),this.detail.photo=this.detail.photo.map(function(t){return t.url=e+t.url,t})}if(this.detail.restore_photo&&"string"==typeof this.detail.restore_photo){var a=this.baseApi;this.detail.restore_photo=JSON.parse(this.detail.restore_photo),this.detail.restore_photo=this.detail.restore_photo.map(function(t){return t.url=a+t.url,t})}if(this.detail.material&&"string"==typeof this.detail.material){var i=this.baseApi;this.detail.material=JSON.parse(this.detail.material),this.detail.material=this.detail.material.map(function(t){return t.url=i+t.url,t})}this.detail.description&&(this.detail.description=this.detail.description.replace(/^<!DOCTYPE html>\s*<html>\s*<head>\s*<\/head>\s*<body>|<\/body>\s*<\/html>\s*$/gi,""))},clickHandler:function(t){this.$peng.isPicture(t.name)?(this.dialogImageUrl=t.url,this.dialogVisible=!0):this.$peng.downloadURI(t.url,t.name)}}},f={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"app-container calendar-list-container"},[a("div",{staticClass:"filter-container"},[a("el-input",{staticClass:"filter-item",staticStyle:{width:"150px"},attrs:{placeholder:t.$t("fossil.name_zh"),clearable:""},nativeOn:{keyup:function(e){if(!("button"in e)&&t._k(e.keyCode,"enter",13,e.key))return null;t.handleFilter(e)}},model:{value:t.listQuery.name_zh,callback:function(e){t.$set(t.listQuery,"name_zh",e)},expression:"listQuery.name_zh"}}),t._v(" "),a("Classification",{attrs:{clfClass:t.clfClass,clfStyle:t.clfStyle,clfClear:t.clfClear,clfOption:t.listQuery.classification},on:{fetchClfData:t.bindClfData,clearClfData:t.clearClfData}}),t._v(" "),a("GeoData",{attrs:{geoClass:t.geoClass,geoStyle:t.geoStyle,geoClear:t.geoClear,geoOption:t.listQuery.geo_age},on:{fetchGeoData:t.bindGeoData,clearGeoData:t.clearGeoData}}),t._v(" "),a("el-button",{directives:[{name:"waves",rawName:"v-waves"}],staticClass:"filter-item",attrs:{size:"mini",type:"primary",icon:"el-icon-search"},on:{click:t.handleFilter}},[t._v(t._s(t.$t("common.table.search")))]),t._v(" "),a("el-button",{staticClass:"filter-item",staticStyle:{"margin-left":"10px"},attrs:{size:"mini",type:"primary",icon:"el-icon-edit"},on:{click:t.handleCreate}},[t._v(t._s(t.$t("common.table.add")))]),t._v(" "),a("el-button",{directives:[{name:"waves",rawName:"v-waves"}],staticClass:"filter-item",attrs:{size:"mini",type:"primary",loading:t.downloadLoading,icon:"el-icon-download"},on:{click:t.handleDownload}},[t._v(t._s(t.$t("common.table.export")))])],1),t._v(" "),a("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],key:t.tableKey,staticStyle:{width:"100%"},attrs:{data:t.list,"element-loading-text":"给我一点时间",border:"",fit:"","highlight-current-row":"",stripe:"",mini:""}},[a("el-table-column",{attrs:{type:"expand"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("el-form",{staticClass:"list-table-expand",attrs:{"label-position":"left",inline:""}},[a("el-form-item",{attrs:{label:t.$t("fossil.owner")}},[a("span",[t._v(t._s(e.row.owner))])]),t._v(" "),a("el-form-item",{attrs:{label:t.$t("fossil.longitude")}},[a("span",[t._v(t._s(e.row.longitude))])]),t._v(" "),a("el-form-item",{attrs:{label:t.$t("fossil.geo_layer")}},[a("span",[t._v(t._s(e.row.geo_layer))])]),t._v(" "),a("el-form-item",{attrs:{label:t.$t("fossil.latitude")}},[a("span",[t._v(t._s(e.row.latitude))])]),t._v(" "),a("el-form-item",{attrs:{label:t.$t("fossil.geo_age")}},[a("span",[t._v(t._s(e.row.geo_age_name))])]),t._v(" "),a("el-form-item",{attrs:{label:t.$t("fossil.altitude")}},[a("span",[t._v(t._s(e.row.altitude))])])],1)]}}])}),t._v(" "),a("el-table-column",{attrs:{width:"165px",align:"center",label:t.$t("fossil.serial_no")},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.serial_no))])]}}])}),t._v(" "),a("el-table-column",{attrs:{width:"130px",align:"center",label:t.$t("fossil.get_time")},scopedSlots:t._u([{key:"default",fn:function(e){return[a("i",{staticClass:"el-icon-time"}),t._v(" "),a("span",[t._v(t._s(t._f("parseTime")(e.row.get_time,"{y}-{m}-{d}")))])]}}])}),t._v(" "),a("el-table-column",{attrs:{"min-width":"150px",label:t.$t("fossil.name_zh")},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",{staticClass:"link-type",on:{click:function(a){t.handleUpdate(e.row)}}},[t._v(t._s(e.row.name_zh))]),t._v(" "),e.row.classification_name?a("el-tag",[t._v(t._s(e.row.classification_name))]):t._e()]}}])}),t._v(" "),a("el-table-column",{attrs:{width:"110px",align:"center",label:t.$t("fossil.name_en")},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.name_en))])]}}])}),t._v(" "),a("el-table-column",{attrs:{width:"100px",align:"center",label:t.$t("fossil.district")},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.district_name))])]}}])}),t._v(" "),a("el-table-column",{attrs:{width:"110px",align:"center",label:t.$t("fossil.classification")},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.classification_name))])]}}])}),t._v(" "),a("el-table-column",{attrs:{"class-name":"status-col",label:t.$t("fossil.is_public"),width:"100px"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("el-switch",{attrs:{"active-color":"#13ce66","inactive-color":"#ff4949"},on:{change:function(a){t.handleSwitchPublic(e.row,e.row.is_public)}},model:{value:e.row.is_public,callback:function(a){t.$set(e.row,"is_public",a)},expression:"scope.row.is_public"}})]}}])}),t._v(" "),a("el-table-column",{attrs:{align:"center",label:t.$t("common.table.action"),width:"230","class-name":"small-padding fixed-width",fixed:"right"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("el-button",{attrs:{type:"primary",icon:"el-icon-edit",size:"mini"},on:{click:function(a){t.handleUpdate(e.row)}}},[t._v(t._s(t.$t("common.table.edit")))]),t._v(" "),a("el-button",{attrs:{type:"primary",icon:"el-icon-info",size:"mini"},on:{click:function(a){t.handleDetail(e.row)}}},[t._v(t._s(t.$t("common.table.detail")))]),t._v(" "),a("el-button",{attrs:{type:"primary",icon:"el-icon-delete",size:"mini"},on:{click:function(a){t.handleDelete(e.row)}}},[t._v(t._s(t.$t("common.table.delete")))])]}}])})],1),t._v(" "),a("div",{staticClass:"pagination-container"},[a("el-pagination",{attrs:{background:"","current-page":t.listQuery.page,"page-sizes":[10,20,30,50],"page-size":t.listQuery.limit,layout:"total, sizes, prev, pager, next, jumper",total:t.total},on:{"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange,"update:currentPage":function(e){t.$set(t.listQuery,"page",e)}}})],1),t._v(" "),a("el-dialog",{attrs:{title:t.$t("common.table.detail"),visible:t.dialogDetailVisible,id:"detail",top:"8vh"},on:{"update:visible":function(e){t.dialogDetailVisible=e}}},[a("el-tabs",{model:{value:t.photoTabs,callback:function(e){t.photoTabs=e},expression:"photoTabs"}},[a("el-tab-pane",{attrs:{label:"照片",name:"photo"}},[a("el-carousel",{attrs:{interval:5e3,arrow:"always",height:"200px"}},t._l(t.detail.photo,function(t){return a("el-carousel-item",{key:t.uid},[a("img",{attrs:{src:t.url,height:"200px"}})])}))],1),t._v(" "),a("el-tab-pane",{attrs:{label:"复原图",name:"restore_photo"}},[a("el-carousel",{attrs:{interval:5e3,arrow:"always",height:"200px"}},t._l(t.detail.restore_photo,function(t){return a("el-carousel-item",{key:t.uid},[a("img",{attrs:{src:t.url,height:"200px"}})])}))],1)],1),t._v(" "),a("el-collapse",{model:{value:t.activeNames,callback:function(e){t.activeNames=e},expression:"activeNames"}},[a("el-collapse-item",{staticClass:"title",attrs:{title:"护照信息",name:"passport"}},[a("el-row",{staticClass:"row-css"},[a("el-col",{attrs:{span:4}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.serial_no"))+":")])]),t._v(" "),a("el-col",{attrs:{span:8}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.serial_no))])]),t._v(" "),a("el-col",{attrs:{span:4}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.name_zh"))+":")])]),t._v(" "),a("el-col",{attrs:{span:8}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.name_zh))])])],1),t._v(" "),a("el-row",{staticClass:"row-css"},[a("el-col",{attrs:{span:4}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.name_en"))+":")])]),t._v(" "),a("el-col",{attrs:{span:8}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.name_en))])]),t._v(" "),a("el-col",{attrs:{span:4}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.owner"))+":")])]),t._v(" "),a("el-col",{attrs:{span:8}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.owner))])])],1),t._v(" "),a("el-row",[a("el-col",{attrs:{span:4}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.get_time"))+":")])]),t._v(" "),a("el-col",{attrs:{span:8}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.get_time))])]),t._v(" "),a("el-col",{attrs:{span:4}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.classification"))+":")])]),t._v(" "),a("el-col",{attrs:{span:8}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.classification_name))])])],1)],1),t._v(" "),a("el-collapse-item",{staticClass:"title",attrs:{title:"标记信息(类型与特征信息)",name:"identify"}},[a("el-row",{staticClass:"row-css"},[a("el-col",{attrs:{span:3}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.district"))+":")])]),t._v(" "),a("el-col",{attrs:{span:9}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.district_name))])]),t._v(" "),a("el-col",{attrs:{span:3}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.address"))+":")])]),t._v(" "),a("el-col",{attrs:{span:9}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.address))])])],1),t._v(" "),a("el-row",{staticClass:"row-css"},[a("el-col",{attrs:{span:3}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.geo_age"))+":")])]),t._v(" "),a("el-col",{attrs:{span:9}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.geo_age_name))])]),t._v(" "),a("el-col",{attrs:{span:3}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.geo_layer"))+":")])]),t._v(" "),a("el-col",{attrs:{span:9}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.geo_layer))])])],1),t._v(" "),a("el-row",{staticClass:"row-css"},[a("el-col",{attrs:{span:3}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.longitude"))+":")])]),t._v(" "),a("el-col",{attrs:{span:9}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.longitude))])]),t._v(" "),a("el-col",{attrs:{span:3}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.latitude"))+":")])]),t._v(" "),a("el-col",{attrs:{span:9}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.latitude))])])],1),t._v(" "),a("el-row",[a("el-col",{attrs:{span:3}},[a("div",{staticClass:"grid-content col-label"},[t._v(t._s(t.$t("fossil.altitude"))+":")])]),t._v(" "),a("el-col",{attrs:{span:9}},[a("div",{staticClass:"grid-content col-content"},[t._v(t._s(t.detail.altitude))])]),t._v(" "),a("el-col",{attrs:{span:3}},[a("div",{staticClass:"grid-content col-label"})]),t._v(" "),a("el-col",{attrs:{span:9}},[a("div",{staticClass:"grid-content col-content"})])],1)],1),t._v(" "),a("el-collapse-item",{staticClass:"title",attrs:{title:"基本特征特性描述信息",name:"description"}},[a("el-row",[a("el-col",{attrs:{span:24}},[a("div",{staticClass:"grid-content  row-css"},[t._v(t._s(t.detail.abstract))])]),t._v(" "),a("el-col",{attrs:{span:24}},[a("div",{staticClass:"grid-content",domProps:{innerHTML:t._s(t.detail.description)}},[t._v(t._s(t.detail.description))])])],1)],1),t._v(" "),a("el-collapse-item",{staticClass:"title",attrs:{title:"相关资料信息",name:"attachment"}},[a("el-row",[a("el-col",{attrs:{span:24}},[a("div",{staticClass:"grid-content"},[a("ol",{staticClass:"el-upload-list el-upload-list--text",attrs:{id:"att"}},t._l(t.detail.material,function(e){return a("li",{key:e.uid,staticClass:"el-upload-list__item is-success",on:{click:function(a){t.clickHandler(e)}}},[a("i",{staticClass:"el-icon-document"}),t._v("\n                  "+t._s(e.name)+"\n                ")])}))])])],1)],1)],1)],1),t._v(" "),a("el-dialog",{attrs:{visible:t.dialogVisible},on:{"update:visible":function(e){t.dialogVisible=e}}},[a("img",{attrs:{width:"100%",src:t.dialogImageUrl,alt:""}})])],1)},staticRenderFns:[]};var v=a("VU/8")(m,f,!1,function(t){a("TEOP")},null,null);e.default=v.exports},oM7Q:function(t,e,a){a("sF+V");var i=a("FeBl").Object;t.exports=function(t,e){return i.create(t,e)}},"sF+V":function(t,e,a){var i=a("kM2E");i(i.S,"Object",{create:a("Yobk")})},z4F4:function(t,e,a){(t.exports=a("FZ+f")(!1)).push([t.i,".waves-ripple {\r\n    position: absolute;\r\n    border-radius: 100%;\r\n    background-color: rgba(0, 0, 0, 0.15);\r\n    background-clip: padding-box;\r\n    pointer-events: none;\r\n    -webkit-user-select: none;\r\n    -moz-user-select: none;\r\n    -ms-user-select: none;\r\n    user-select: none;\r\n    -webkit-transform: scale(0);\r\n    transform: scale(0);\r\n    opacity: 1;\r\n}\r\n\r\n.waves-ripple.z-active {\r\n    opacity: 0;\r\n    -webkit-transform: scale(2);\r\n    transform: scale(2);\r\n    -webkit-transition: opacity 1.2s ease-out, -webkit-transform 0.6s ease-out;\r\n    transition: opacity 1.2s ease-out, -webkit-transform 0.6s ease-out;\r\n    transition: opacity 1.2s ease-out, transform 0.6s ease-out;\r\n    transition: opacity 1.2s ease-out, transform 0.6s ease-out, -webkit-transform 0.6s ease-out;\r\n}",""])}});