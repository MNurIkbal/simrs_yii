'use strict';

var Jadwalpoli = function() {
	this.scope = {
		hourStart: 9,
		hourEnd: 17
	};
	this.rows = [];
	this.jadwal = [];
	this.totalRow = 0;
	this.days = [];
	this.poliklinik = [];
};

Jadwalpoli.Renderer = function(jp) {
	if (!(jp instanceof Jadwalpoli)) {
		throw new Error('Terjadi Kesalahan');
	}
	this.jadwalpoli = jp;
};


(function() {
	function dayExistsIn(d, days) {
		return days.indexOf(d) !== -1;
	}
	Jadwalpoli.prototype = {
		setTotalRow: function(total) {
			this.totalRow = total;

			return this;
		},
		addDays: function(newDays) {
			function hasProperFormat() {
				return newDays instanceof Array;
			}

			var existingDays = this.days;

			if (hasProperFormat()) {
				newDays.forEach(function(d) {
					if (!dayExistsIn(d, existingDays)) {
						existingDays.push(d);
					} else {
						throw new Error('Day already exists');
					}
				});
			} else {
				throw new Error('Tried to add day in wrong format');
			}

			return this;
		},
		addJadwal: function(poli, day, mulai, selesai, kuota) {
			// if (!locationExistsIn(location, this.locations)) {
			// 	throw new Error('Unknown location');
			// }
			// if (!isValidTimeRange(start, end)) {
			// 	throw new Error('Invalid time range: ' + JSON.stringify([start, end]));
			// }

			// var optionsHasValidType = Object.prototype.toString.call(options) === '[object Object]';

			var existingPoli = this.poliklinik;
			if (!dayExistsIn(poli, existingPoli)) {
				existingPoli.push(poli);
			}

			this.jadwal.push({
				poli: poli,
				day: day,
				mulai: mulai,
				selesai: selesai,
				kuota: kuota
				// options: optionsHasValidType ? options : undefined
			});

			return this;
		}
	};
	function emptyNode(node) {
		while (node.firstChild) {
			node.removeChild(node.firstChild);
		}
	}

	Jadwalpoli.Renderer.prototype = {
		drawList: function(selector) {
			function checkContainerPrecondition(container) {
				if (container === null) {
					throw new Error('Jadwalpoli container not found');
				}
			}
			function generateRow(container) {
				for (var k=0; k<jadwalpoli.poliklinik.length; k++) {
					var rowNode = container.appendChild(document.createElement('tr'));
					appendColumnPoli(rowNode,jadwalpoli.poliklinik[k]);
				}
			}
			function appendColumnPoli(node,poli) {
				var poliNode = node.appendChild(document.createElement('td'));
				poliNode.textContent = poli;
				for (var k=0; k<jadwalpoli.days.length; k++) {
					var dayNode = node.appendChild(document.createElement('td'));
					appendDayJadwal(jadwalpoli.days[k],dayNode,poli);
				}		
			}
			function appendDayJadwal(day, node,poli) {
				for (var k=0; k<jadwalpoli.jadwal.length; k++) {
					var schedule = jadwalpoli.jadwal[k];
					if (schedule.day === day && schedule.poli === poli) {
						var text_desc = "Jam Buka: "+schedule.mulai+" s/d "+schedule.selesai+" | Max Antrian: "+schedule.kuota;
						node.textContent = text_desc;
					}
				}
			}
			var jadwalpoli = this.jadwalpoli;
			var container = selector;
			checkContainerPrecondition(container);
			emptyNode(container);
			generateRow(container);
		}
	};

})();