/* global chargePointData, flatpickr */
document.addEventListener('DOMContentLoaded', () => {
    /* ---------- 1.  Date Picker ------------------------------------ */
    const booked = chargePointData.bookedSlots.map(s => [
      s.booking_date.split(' ')[0],   // Y-m-d
      s.due_date.split(' ')[0]
    ]);
  
    const fp = flatpickr('#date-picker', {
      dateFormat: 'Y-m-d',
      altInput : true,
      altFormat: 'F j, Y',
      minDate  : 'today',
      maxDate  : new Date().fp_incr(30),   // +30 days
      disable  : booked,                   // disable full days that are booked
      onChange : ([d]) => {
        const ymd = fp.formatDate(d, 'Y-m-d');
        document.getElementById('selected-date-display').textContent = fp.altInput.value;
        document.getElementById('booking_date').value = ymd;   // hidden date field
        populateStartTimes(ymd);
      }
    });
  
    /* ---------- 2.  Controls -------------------------------------- */
    const startSel  = document.getElementById('start-time');
    const durSel    = document.getElementById('duration');
    const dueField  = document.getElementById('due_date');
    const priceLbl  = document.getElementById('price-display');
    const durLbl    = document.getElementById('duration-display');
    const energyLbl = document.getElementById('energy-display');
    const estBox    = document.getElementById('charging-estimate');
    const submitBtn = document.getElementById('submit-booking');
  
    /* ---------- 3.  Populate start-time list ---------------------- */
    function populateStartTimes(ymd) {
      startSel.innerHTML = '<option value="">Loading…</option>';
      durSel.innerHTML   = '<option value="">Select start time first</option>';
      submitBtn.disabled = true;
      estBox.style.display = 'none';
  
      /* 48 half-hour slots */
      const all = Array.from({length: 48}, (_, i) =>
        ('0' + Math.floor(i/2)).slice(-2) + ':' + (i%2 ? '30' : '00'));
  
      /* remove any slot that overlaps an existing booking */
      const dayBookings = chargePointData.bookedSlots.filter(
        s => s.booking_date.startsWith(ymd)
      );
      const free = all.filter(t => {
        return !dayBookings.some(b => {
          const start = b.booking_date.substr(11,5);
          const end   = b.due_date.substr(11,5);
          return (t >= start) && (t < end);
        });
      });
  
      startSel.innerHTML = free.length
        ? '<option value="">Choose…</option>' +
          free.map(t => `<option value="${t}">${t}</option>`).join('')
        : '<option value="">Fully booked</option>';
    }
  
    /* ---------- 4.  Populate duration after start-time chosen ----- */
    startSel.addEventListener('change', () => {
      const start = startSel.value;
      durSel.innerHTML = '<option value="">Choose…</option>';
      if (!start) return;
  
      const [sh, sm] = start.split(':').map(Number);
      const startIdx = sh*2 + (sm===30?1:0);
  
      /* convert existing bookings to half-hour indices for collision */
      const booked = chargePointData.bookedSlots
        .filter(s => s.booking_date.split(' ')[0] === document.getElementById('booking_date').value)
        .map(b => {
          const [bh, bm] = b.booking_date.substr(11,5).split(':').map(Number);
          const [eh, em] = b.due_date.substr(11,5).split(':').map(Number);
          return {from: bh*2+(bm===30?1:0), to: eh*2+(em===30?1:0)};
        });
  
      /* allow 0.5 h .. 4 h if no overlap */
      for (let half=1; half<=8; half++) {
        const endIdx = startIdx + half;
        const collides = booked.some(b => startIdx < b.to && endIdx > b.from);
        if (collides) break;
        const hours = half / 2;
        durSel.insertAdjacentHTML('beforeend',
          `<option value="${hours}">${hours} hour${hours>1?'s':''}</option>`);
      }
    });
  
    /* ---------- 5.  Show estimate + compute due_date ------------- */
    durSel.addEventListener('change', () => {
      const dur   = parseFloat(durSel.value || '0');   // hours
      const start = startSel.value;
      if (!dur || !start) { submitBtn.disabled = true; return; }
  
      /* human-readable estimate */
      durLbl.textContent    = dur + (dur>1 ? ' hours' : ' hour');
      energyLbl.textContent = (dur * 7).toFixed(1) + ' kWh';   // 7 kW charger
      priceLbl.textContent  = '$' + (dur*7*chargePointData.pricePerKwh).toFixed(2);
      estBox.style.display  = 'block';
      submitBtn.disabled    = false;
  
      /* hidden full Y-m-d H:i:s for server */
      const ymd = document.getElementById('booking_date').value;           // Y-m-d
      const [h,m] = start.split(':').map(Number);
      const end   = new Date(`${ymd}T${start}:00`);
      end.setMinutes(end.getMinutes() + dur*60);
  
      dueField.value = [
        ymd,
        ('0'+end.getHours()).slice(-2) + ':' +
        ('0'+end.getMinutes()).slice(-2) + ':00'
      ].join(' ');
    });
  });
  