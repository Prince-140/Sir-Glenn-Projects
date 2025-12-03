// Navigation functionality
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all links
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        
        // Add active class to clicked link
        this.classList.add('active');
        
        // Hide all content sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Show the selected section
        const sectionId = this.getAttribute('data-section');
        document.getElementById(sectionId).classList.add('active');
        
        // Update page title
        const titles = {
            'dashboard': 'Dashboard Overview',
            'appliances': 'Appliances Control',
            'energy': 'Energy Monitor',
            'summary': 'Usage Summary',
            'logs': 'Activity Logs',
            'settings': 'System Settings'
        };
        document.getElementById('page-title').textContent = titles[sectionId] || 'Dashboard';
        
        // Re-run animations for the new section
        setTimeout(animateDashboard, 100);
    });
});

// Modal functionality for adding appliances
const addApplianceBtn = document.getElementById('add-appliance-btn');
const addApplianceModal = document.getElementById('add-appliance-modal');
const cancelModalBtn = document.getElementById('cancel-modal');

if (addApplianceBtn && addApplianceModal) {
    addApplianceBtn.addEventListener('click', () => {
        addApplianceModal.classList.add('active');
    });

    if (cancelModalBtn) {
        cancelModalBtn.addEventListener('click', () => {
            addApplianceModal.classList.remove('active');
        });
    }

    // Close modal when clicking outside
    addApplianceModal.addEventListener('click', (e) => {
        if (e.target === addApplianceModal) {
            addApplianceModal.classList.remove('active');
        }
    });
}

// Handle appliance toggle switches
document.addEventListener('change', function(event) {
    if (event.target.classList.contains('appliance-switch')) {
        const id = event.target.getAttribute('data-id');
        if (event.target.checked) {
            // Submit turn ON form
            const onForm = document.querySelector(`.turn-on-form[data-id="${id}"]`);
            if (onForm) onForm.submit();
        } else {
            // Submit turn OFF form
            const offForm = document.querySelector(`.turn-off-form[data-id="${id}"]`);
            if (offForm) offForm.submit();
        }
    }
});

// Simulate real-time data updates
function updateDashboard() {
    // PIR value (motion detection)
    const pirValue = Math.floor(Math.random() * 50) + 200;
    const pirElement = document.getElementById('pir-value');
    if (pirElement) pirElement.textContent = pirValue;

    // LDR value (light sensor)
    const ldrValue = Math.floor(Math.random() * 200) + 600;
    const ldrElement = document.getElementById('ldr-value');
    if (ldrElement) ldrElement.textContent = ldrValue;

    // Current value
    const current = (Math.random() * 2 + 11).toFixed(1);
    const currentElement = document.getElementById('current-value');
    if (currentElement) currentElement.textContent = current + ' A';

    // Wattage
    const wattage = (parseFloat(current) * 220 / 1000).toFixed(2);
    const wattElement = document.getElementById('watt-value');
    if (wattElement) wattElement.textContent = wattage + ' kW';

    // Daily cost
    const dailyCost = (parseFloat(wattage) * 24 * 1.0).toFixed(2);
    const costDayElement = document.getElementById('cost-day');
    if (costDayElement) costDayElement.textContent = '$' + dailyCost;

    // Monthly cost
    const monthlyCost = Math.floor(parseFloat(dailyCost) * 30);
    const costMonthElement = document.getElementById('cost-month');
    if (costMonthElement) costMonthElement.textContent = '$' + monthlyCost;
}

// Update every 3 seconds
setInterval(updateDashboard, 3000);
updateDashboard();

/* ---------- Appliance Donut Chart ---------- */
function parseWatts(text){
    if(!text) return 0;
    const num = (''+text).replace(/[^0-9\.]/g,'');
    return parseFloat(num) || 0;
}

function getApplianceData(){
    // This function would typically get data from PHP/backend
    // For now, we'll use static data or parse from table if available
    const appliances = [
        { name: 'Living Room Light', watts: 150 },
        { name: 'Ceiling Fan', watts: 75 },
        { name: 'Air Conditioner', watts: 1200 }
    ];
    return appliances.filter(d=>d.watts>0);
}

function renderApplianceDonut(){
    const data = getApplianceData();
    const canvas = document.getElementById('appliance-donut-canvas');
    const legend = document.getElementById('appliance-legend');
    if(!canvas) return;
    
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0,0,canvas.width,canvas.height);
    if(legend) legend.innerHTML = '';
    
    const total = data.reduce((s,d)=>s+d.watts,0);
    const cx = 250, cy = 160, rOuter = 100, rInner = 60;
    const colors = ['#FF7043','#FFB74D','#FFD54F','#4dd0e1','#52acbc','#66bb6a','#9575cd'];
    
    if(total === 0){
        ctx.fillStyle = '#bbb';
        ctx.font = '16px Poppins, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('No usage data', cx, cy);
        return;
    }
    
    let angle = -0.5 * Math.PI;
    data.forEach((d,i)=>{
        const portion = d.watts/total;
        const endAngle = angle + portion*2*Math.PI;
        
        // Draw slice
        ctx.beginPath();
        ctx.arc(cx, cy, rOuter, angle, endAngle);
        ctx.arc(cx, cy, rInner, endAngle, angle, true);
        ctx.closePath();
        ctx.fillStyle = colors[i%colors.length];
        ctx.globalAlpha = 0.92;
        ctx.fill();
        ctx.globalAlpha = 1;
        
        // Percent label
        if(portion > 0.08){
            const mid = angle + (endAngle-angle)/2;
            const lx = cx + Math.cos(mid)*((rOuter+rInner)/2);
            const ly = cy + Math.sin(mid)*((rOuter+rInner)/2);
            ctx.fillStyle = '#fff';
            ctx.font = 'bold 13px Poppins, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(Math.round(portion*100)+'%', lx, ly+4);
        }
        
        // Legend
        if(legend){
            const item = document.createElement('div');
            item.className = 'legend-item';
            const colorBox = document.createElement('div');
            colorBox.className = 'legend-color';
            colorBox.style.background = colors[i%colors.length];
            colorBox.style.width = '16px';
            colorBox.style.height = '16px';
            colorBox.style.display = 'inline-block';
            colorBox.style.borderRadius = '3px';
            colorBox.style.marginRight = '8px';
            const label = document.createElement('span');
            label.innerHTML = `<strong>${d.name}</strong> - ${d.watts}W (${Math.round(portion*100)}%)`;
            item.appendChild(colorBox);
            item.appendChild(label);
            legend.appendChild(item);
        }
        
        angle = endAngle;
    });
    
    // Donut center
    ctx.save();
    ctx.beginPath();
    ctx.arc(cx, cy, rInner-2, 0, 2*Math.PI);
    ctx.fillStyle = '#fff';
    ctx.shadowColor = 'rgba(0,0,0,0.04)';
    ctx.shadowBlur = 8;
    ctx.fill();
    ctx.restore();
    
    // Total text
    ctx.fillStyle = '#52acbc';
    ctx.font = 'bold 22px Poppins, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(total+' W', cx, cy+8);
}

// Profile photo functionality
const profileInput = document.getElementById('profile-photo-input');
const profileAvatar = document.getElementById('profile-avatar');
const removePhotoBtn = document.getElementById('remove-photo-btn');

function loadProfilePhoto(){
    try{
        const data = localStorage.getItem('profilePhoto');
        if(data && /^data:image\//.test(data)){
            profileAvatar.style.backgroundImage = `url(${data})`;
            profileAvatar.classList.add('has-photo');
            if(removePhotoBtn) removePhotoBtn.style.display = 'inline-block';
        } else {
            profileAvatar.style.backgroundImage = '';
            profileAvatar.classList.remove('has-photo');
            if(removePhotoBtn) removePhotoBtn.style.display = 'none';
        }
    }catch(e){
        console.error('Failed to load profile photo', e);
    }
}

if(profileAvatar){
    profileAvatar.addEventListener('click', ()=> {
        if(profileInput) profileInput.click();
    });
    
    profileAvatar.addEventListener('keypress', (e)=> { 
        if(e.key === 'Enter' && profileInput) profileInput.click(); 
    });
}

if(profileInput){
    profileInput.addEventListener('change', (e)=>{
        const file = e.target.files && e.target.files[0];
        if(!file) return;
        
        if(!file.type.startsWith('image/')) {
            alert('Please select a valid image file.');
            return;
        }
        
        if(file.size > 2*1024*1024){
            alert('Image is too large. Please select a file under 2MB.');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = ()=>{
            try{
                localStorage.setItem('profilePhoto', reader.result);
                loadProfilePhoto();
            }catch(err){
                alert('Failed to save photo.');
                console.error('Failed to save photo', err);
            }
        };
        reader.readAsDataURL(file);
    });
}

if(removePhotoBtn){
    removePhotoBtn.addEventListener('click', ()=>{
        localStorage.removeItem('profilePhoto');
        if(profileInput) profileInput.value = '';
        loadProfilePhoto();
    });
}

// Load profile photo on init
loadProfilePhoto();

// Entrance animations for dashboard content
function animateDashboard(){
    const selectors = [
        '.topbar',
        '.cards-grid .card',
        '.appliance-grid .appliance-card',
        '.chart-container',
        '.table-container'
    ];
    
    let items = [];
    selectors.forEach(sel=> {
        const elements = document.querySelectorAll(sel);
        items = items.concat(Array.from(elements));
    });
    
    // Remove duplicates and only visible elements
    items = items.filter((v,i,a)=> a.indexOf(v) === i && v.offsetParent !== null);

    items.forEach(el=> el.classList.add('animate-item'));
    items.forEach((el,i)=> setTimeout(()=>{
        el.classList.add('show');
        
        // Reveal chart svg more prominently
        if(el.querySelectorAll){
            const svgs = el.querySelectorAll('.chart-svg');
            svgs.forEach(s=> s.classList.add('show'));
        }
    }, 80 * i));
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Run animations after small delay so images/styles are ready
    setTimeout(animateDashboard, 250);
    
    // Render donut chart
    renderApplianceDonut();
});