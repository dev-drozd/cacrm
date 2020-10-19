var makeStat = {
	g: {}, 
	xmlns: 'http://www.w3.org/2000/svg',
	init: function(o) {
		var bWidth = o.w || 900,
			bHeight = o.h || 450,
			s = o.s,
			cx = (o.x[1] - o.x[0] + o.x[2]) || 1,
			cy = (o.y[1] - o.y[0] + o.y[2]) || 1,
			csx = cx / (o.x[2] || 1) || 1,
			csy = cy / (o.y[2] || 1) || 1,
			dx = (bWidth - s * 2) / cx,
			dy = (bHeight - s * 2) / cy,
			sx = dx * o.x[2],
			sy = dy * o.y[2],
			rl = 0,
			rl_n = 0,
			bl = [];
			vl = 0;
			cl = [];

		// svg create
		var svgEl = document.createElementNS(makeStat.xmlns, 'svg');
		svgEl.setAttribute('viewBox', '0 0 ' + bWidth + ' ' + bHeight);
		svgEl.setAttribute('version', '2.0');
		svgEl.setAttribute('width', bWidth);
		svgEl.setAttribute('height', bHeight);
		svgEl.style.display = 'block';
		svgEl.style.background = '#fff';
		svgEl.style.maxWidth = '100%';
		svgEl.style.height = 'auto';

		var svgCtnr = document.getElementById(o.id);
    	svgCtnr.appendChild(svgEl);

		// plot area		
		makeStat.g = document.createElementNS(makeStat.xmlns, 'g');
	    svgEl.appendChild(makeStat.g);
		makeStat.g.setAttribute('transform', 'matrix(1,0,0,-1,' + s / 2 + ',' + bHeight + ')');

		// oxy area		
	    var xy = document.createElementNS(makeStat.xmlns, 'g');
		makeStat.g.appendChild(xy);

		// ox
	    makeStat.cLine({
	    	x1: s * 0.5,
	    	y1: s,
	    	x2: bWidth - s,
	    	y2: s,
	    	color: '#333',
	    	bWidth: 0.5
	    }, xy);

		// oy
	    makeStat.cLine({
	    	x1: s,
	    	y1: s * 0.5,
	    	x2: s,
	    	y2: bHeight - s * 0.5,
	    	color: '#333',
	    	bWidth: 0.5
		}, xy);
		
		// grid area
		var grid = document.createElementNS(makeStat.xmlns, 'g');
		makeStat.g.appendChild(grid);
		
		// x ordinates
		for (var i = 0; i < csx; i++) {
			makeStat.cLine({
				x1: sx * i + s,
				y1: s * 0.75,
				x2: sx * i + s,
				y2: bHeight - s * 0.5,
				color: i ? '#bbb' : 'transparent',
				bWidth: 0.5
			}, grid);

			if (o.x[3]) {
				makeStat.cText({
					x: sx * i + s,
					y: s * 0.75,
					color: '#777',
					fontFamily: 'Consolas',
					anchor: 'middle',
					text: o.x[3][i] || i * o.x[2] + o.x[0],
					s: s
				}, grid);
			}
		}
	
		// y ordinates
		for (i = 0; i < csy; i++) {
			rlNumber = (o.y[2] ? o.y[3][i] || i * o.y[2] + o.y[0] : 0).toFixed(0);
			if ((rlNumber == o.red_line || rlNumber > o.red_line) && !rl) {
				rl_n = rlNumber; 
				rl = 1;
			}
			
			makeStat.cLine({
				x1: s * 0.75,
				y1: (sy * i + s) || s,
				x2: bWidth - s,
				y2: (sy * i + s) || s,
				color: i ? '#bbb' : 'transparent',
				bWidth: 0.5
			}, grid);

			if (o.y[3]) {
				makeStat.cText({
					x: 0,
					y: (sy * i + s + s * 0.45) || s + s * 0.45,
					color: '#777',
					fontFamily: 'Consolas',
					anchor: 'start',
					text: (o.y[2] ? o.y[3][i] || i * o.y[2] + o.y[0] : 0).toFixed(0),
					s: s
				}, grid);
			}
		}
		
		// red line
		if (o.red_line && o.red_line > o.y[0] && o.red_line < o.y[1]) {
			makeStat.cLine({
				x1: s * 0.75,
				y1: (bHeight - s * 2) / (o.y[1] - o.y[0]) * (o.red_line - o.y[0]) + s * 0.65,
				x2: bWidth - s,
				y2: (bHeight - s * 2) / (o.y[1] - o.y[0]) * (o.red_line - o.y[0]) + s * 0.65,
				color: '#f00',
				bWidth: 1
			}, grid);
		}
	
		// lines area
		var lines = document.createElementNS(makeStat.xmlns, 'g');
		makeStat.g.appendChild(lines);

		// points area
		var points = document.createElementNS(makeStat.xmlns, 'g');
		makeStat.g.appendChild(points);
		
		// tips area
		var tips = document.createElementNS(makeStat.xmlns, 'g');
		makeStat.g.appendChild(tips);
	
		if (o.data[0]['points']) {
			// plot		
			var tTip;
			o.data.forEach(function(v, j) {
				for (var i = 0; i < o.data[j]['points'].length; i++) {
					x1 = (o.data[j]['points'][i][0] - o.x[0]) * dx + s;
					y1 = (o.data[j]['points'][i][1] - o.y[0]) * dy + s;
					
					if (o.violet_line)
						vl += o.data[j]['points'][i][1];

					if (o.data[j]['points'][i - 1]) {
						makeStat.cLine({
							x1: (o.data[j]['points'][i-1][0] - o.x[0]) * dx + s,
							y1: (o.data[j]['points'][i-1][1] - o.y[0]) * dy + s,
							x2: x1,
							y2: y1,
							color: o.data[j]['color'],
							bWidth: 2,
							dash: o.data[j]['dash']
						}, lines);
					}
					
					if (o.black_line && o.red_line) {
						if (o.data[j]['points'][i][1] < o.red_line)
							bl.push(o.data[j]['points'][i]);
						else {
							if (bl.length >= o.black_line) {
								var bl_coords = [bl[0][0], bl[0][1]];
								for(var n = 1; n < bl.length; n ++) {
									if (bl[n][1] > bl_coords[1])
										bl_coords[1] = bl[n][1];
								}
								makeStat.cLine({
									x1: (bl_coords[0] - o.x[0]) * dx + s,
									y1: (bl_coords[1] - o.y[0]) * dy + s,
									x2: (o.data[j]['points'][i-1][0] - o.x[0]) * dx + s,
									y2: (bl_coords[1] - o.y[0]) * dy + s,
									color: '#000000',
									bWidth: 2
								}, lines);
							}
							bl = [];
						}
					}
					
					if (o.compare_line) {
						if (cl.length < o.compare_line && i < o.data[j]['points'].length - 1)
							cl.push(o.data[j]['points'][i]);
						else {
							var cl_coords = [cl[0][0], cl[0][1]];
							for(var n = 1; n < cl.length; n ++) {
									cl_coords[1] += cl[n][1];
							}
							cl_coords[1] /= cl.length;
							makeStat.cLine({
								x1: (cl_coords[0] - o.x[0]) * dx + s,
								y1: (cl_coords[1] - o.y[0]) * dy + s,
								x2: (o.data[j]['points'][i-1][0] - o.x[0]) * dx + s,
								y2: (cl_coords[1] - o.y[0]) * dy + s,
								color: '#ffb352',
								bWidth: 2
							}, lines);

							cl = [];
							cl.push(o.data[j]['points'][i]);
						}
					}

					makeStat.cCircle({
						cx: x1,
						cy: y1,
						r: 5,
						fill: o.data[j]['color'],
						color: '#f6f6f6',
						bWidth: 0,
						class: 'point',
						onmouseover: 'makeStat.showTips(' + i + ', \'' + o.id + '\', \'' + (o.data[j]['id'] || '') + '\')',
						onmouseout: 'makeStat.hideTips(' + i + ', \'' + o.id + '\', \'' + (o.data[j]['id'] || '') + '\')'
					}, points);
					
					tTip = document.createElementNS(makeStat.xmlns, 'g');
					tTip.setAttribute('class', 'tTip');
					tTip.setAttribute('id', o.id + (o.data[j]['id'] ? '_' + o.data[j]['id'] + '_' : '') + '_tTip_' + i);
					tips.appendChild(tTip);

					tBack = makeStat.cRect({
						x: x1 + 18,
						y: y1,
						width: 40,
						height: 10
					}, tTip);
					
					tNode = makeStat.cTTip({
						x: x1 + 18,
						y: y1,
						color: '#ddd',
						fontFamily: 'Consolas',
						text: o.data[j]['points'][i][2] || '',
						s: s,
						bHeight: bHeight,
						px: o.data[j]['points'][i][0],
						py: o.data[j]['points'][i][1],
						tx: o.x[4],
						ty: o.y[4],
						plusx: o.plusx
					}, tTip);
					
					tTipWidth = tNode.getBBox().width;
					if (tNode.childNodes.length > 1) {
						if (tNode.childNodes[0].getComputedTextLength() > tNode.childNodes[1].getComputedTextLength())
							tTipWidth = tNode.childNodes[0].getComputedTextLength();
						else 
							tTipWidth = tNode.childNodes[1].getComputedTextLength();
						tNode.childNodes[1].setAttribute('dx', -tNode.childNodes[0].getComputedTextLength());
					}
					tBack.setAttribute('width', tTipWidth + 20);		
					tBack.setAttribute('height', tNode.getBBox().height + 20);	
					tBack.setAttribute('y', y1 - tNode.getBBox().height / 2 - 10);	
					tNode.setAttribute('y', y1 - tNode.getBBox().height / 2 - 17.5);
					
					if (tTipWidth > bWidth - s * 0.5 - x1) {
						makeStat.cPolygon({
							points: (x1 - 14) + ', ' + y1 + ', ' +
									(x1 - 18) + ', ' + (y1 - 3) + ', ' +
									(x1 - 18) + ', ' + (y1 + 3),
							fill: '#000'
						}, tTip);

						tBack.setAttribute('x', x1 - tTipWidth - 38);
						tNode.setAttribute('x', x1 - tTipWidth - 28);
					} else {
						makeStat.cPolygon({
							points: (x1 + 14) + ', ' + y1 + ', ' +
									(x1 + 18) + ', ' + (y1 - 3) + ', ' +
									(x1 + 18) + ', ' + (y1 + 3),
							fill: '#000'
						}, tTip);
					}
				};
				
				if (vl) {
					vl /= o.data[j]['points'].length;
					makeStat.cLine({
						x1: s * 0.75,
						y1: (vl - o.y[0]) * dy + s,
						x2: bWidth - s,
						y2: (vl - o.y[0]) * dy + s,
						color: '#9b43b1',
						bWidth: 2
					}, lines);
				}
			});
		}
		
	}, cLine: function(o, g) {
		var line = document.createElementNS(makeStat.xmlns, 'line');
	    line.setAttribute('x1', o.x1);
	    line.setAttribute('y1', o.y1);
	    line.setAttribute('x2', o.x2);
	    line.setAttribute('y2', o.y2);
	    line.setAttribute('stroke', o.color);
	    line.setAttribute('stroke-width', o.bWidth);
		if (o.dash)
			line.setAttribute('stroke-dasharray', '10,10');
	    g.appendChild(line);
	}, cPolygon: function(o, g) {
		var polygon = document.createElementNS(makeStat.xmlns, 'polygon');
	    polygon.setAttribute('points', o.points);
	    polygon.setAttribute('fill', o.fill);
	    g.appendChild(polygon);
	}, cCircle: function(o, g) {
		var circle = document.createElementNS(makeStat.xmlns, 'circle');
	    circle.setAttribute('cx', o.cx);
		circle.setAttribute('cy', o.cy);
		circle.setAttribute('r',  o.r);
		circle.setAttribute('fill', o.fill);
		circle.setAttribute('stroke', o.color);
	    circle.setAttribute('stroke-width', o.bWidth);
	    circle.setAttribute('class', o.class);
	    circle.setAttribute('onmouseover', o.onmouseover);
	    circle.setAttribute('onmouseout', o.onmouseout);
	    g.appendChild(circle);
	}, cRect: function(o, g) {
		var rect = document.createElementNS(makeStat.xmlns, 'rect');
	    rect.setAttribute('x', o.x);
	    rect.setAttribute('y', o.y);
	    rect.setAttribute('width', o.width);
	    rect.setAttribute('height', o.height);
		g.appendChild(rect);
		return rect;
	}, cTTip: function(o, g) {
		var text = document.createElementNS(makeStat.xmlns, 'text');
	    text.setAttribute('x', o.x + 10);
	    text.setAttribute('y', o.y);
	    text.setAttribute('text-anchor', 'start');
		text.setAttribute('fill', o.color);
		text.setAttribute('font-family', o.fontFamily);
		text.setAttribute('transform', 'matrix(1,0,0,-1,0,' + (o.y * 2 - o.s / 2) + ')');
		
		var textNode, tspan;
		if (o.text) {
			textNode = document.createTextNode(o.text);
			tspan = document.createElementNS(makeStat.xmlns, 'tspan');
			tspan.appendChild(textNode);
			text.appendChild(tspan);
		}
		textNode = document.createTextNode(o.tx + (o.px + (o.plusx || 0)).toFixed(0) + '; ' + o.ty + o.py.toFixed(0) + ';');
		tspan = document.createElementNS(makeStat.xmlns, 'tspan');
		tspan.setAttribute('dy', o.text ? '18px' : 0);
		tspan.appendChild(textNode);
		text.appendChild(tspan);
		g.appendChild(text);
		return text;
	}, cText: function(o, g) {
		var text = document.createElementNS(makeStat.xmlns, 'text');
	    text.setAttribute('x', o.x);
	    text.setAttribute('y', o.y);
	    text.setAttribute('text-anchor', o.anchor);
		text.setAttribute('fill', o.color);
		text.setAttribute('font-family', o.fontFamily);
		text.setAttribute('transform', 'matrix(1,0,0,-1,0,' + (o.y ? (o.y * 2 - o.s / 2) : 0) + ')');

		var textNode = document.createTextNode(o.text);
		text.appendChild(textNode);
		g.appendChild(text);
		return text;
	}, showTips: function(id, oid, did) {
		document.querySelector('#' + oid + (did ? '_' + did + '_' : '') + '_tTip_' + id).style.opacity = 1;
		document.querySelector('#' + oid + (did ? '_' + did + '_' : '') + '_tTip_' + id).style.visibility = 'visible';
	}, hideTips: function(id, oid, did) {
		document.querySelector('#' + oid + (did ? '_' + did + '_' : '') + '_tTip_' + id).style.opacity = 0;
		document.querySelector('#' + oid + (did ? '_' + did + '_' : '') + '_tTip_' + id).style.visibility = 'hidden';
	}
}