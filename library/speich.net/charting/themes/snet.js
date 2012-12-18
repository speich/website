define([
	"dojox/charting/Theme",
	"dojox/charting/themes/common"
], function(Theme, themes) {

	themes.snet = new Theme({
		chart:{
			titleFont: "normal normal normal 14px Verdana, Tahoma, Helvetica, Arial, sans-serif"
		},
		axis: {
			stroke: {
				color: '#666',
				width: 0.5
			},
			tick: {
       		color: "#666",
       		position: "center",
				titleGap: 10,
       		font: "normal normal normal 9px Verdana, Tahoma, Helvetica, Arial, sans-serif",
				titleFont: "normal normal normal 11px Verdana, Tahoma, Helvetica, Arial, sans-serif",
       		fontColor: "#000"
   		},
			majorTick:	{
				width:  0.5,
				length: 6
			},
			minorTick:	{
				width: 0.5,
				length: 3
			}
		},
		series: {
			stroke: {width: 1},
			outline: null//{width: 0}
		},
		marker: {
			stroke:  {width: 1},
			outline: null//{width: 0}
		},
		seriesThemes: [
			{fill: "#5fa92a", stroke: {color: "#73a94c"}},
			{fill: "#9f4898", stroke: {color: "#9f4898"}}
		],
		markerThemes: [
			{fill: "#5fa92a", stroke: {color: "#73a94c"}},
			{fill: "#9f4898", stroke: {color: "#9f4898"}}
		]
	});

	return themes.snet;
});
