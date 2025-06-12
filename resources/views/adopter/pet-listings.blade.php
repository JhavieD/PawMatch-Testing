@extends('layouts.pet-listings')    

@section('title', 'Pet-Listings - PawMatch')

@section('adopter-content')
<!-- <nav class="navbar">
    <div class="nav-content">
        <a href="index.html" class="logo">🐾 PawMatch</a>
        <div class="nav-links">
            <a href="about.html">About</a>
            <a href="pet-listings.html" style="font-weight: 700;">Find Pets</a>
            <a href="faq.html">FAQ</a>
            <a href="terms.html">Terms</a>
            <a href="{{ route('adopter.dashboard') }}" class="btn">Dashboard</a>
        </div>
    </div>
</nav> -->

<div class="main-container">
    <!-- Filters Panel -->
    <aside class="filters">
        <div class="filter-group">
            <h3>Pet Type</h3>
            <div class="filter-option">
                <label>
                    <input type="checkbox" name="type" value="dog"> Dogs
                </label>
            </div>
            <div class="filter-option">
                <label>
                    <input type="checkbox" name="type" value="cat"> Cats
                </label>
            </div>
            <div class="filter-option">
                <label>
                    <input type="checkbox" name="type" value="other"> Other Pets
                </label>
            </div>
        </div>

        <div class="filter-group">
            <h3>Age</h3>
            <div class="filter-option">
                <label>
                    <input type="checkbox" name="age" value="baby"> Baby
                </label>
            </div>
            <div class="filter-option">
                <label>
                    <input type="checkbox" name="age" value="young"> Young
                </label>
            </div>
            <div class="filter-option">
                <label>
                    <input type="checkbox" name="age" value="adult"> Adult
                </label>
            </div>
            <div class="filter-option">
                <label>
                    <input type="checkbox" name="age" value="senior"> Senior
                </label>
            </div>
        </div>

        <div class="filter-group">
            <h3>Size</h3>
            <div class="filter-option">
                <label>
                    <input type="checkbox" name="size" value="small"> Small
                </label>
            </div>
            <div class="filter-option">
                <label>
                    <input type="checkbox" name="size" value="medium"> Medium
                </label>
            </div>
            <div class="filter-option">
                <label>
                    <input type="checkbox" name="size" value="large"> Large
                </label>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main>
        <div class="search-bar">
            <input type="text" class="search-input" placeholder="Search pets by name, breed, or location...">
        </div>

        <div class="pet-grid">
            <!-- Sample Pet Cards -->
            <div class="pet-card">
                <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482958427_546890997878558_7939868464340469320_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEg26tmyKvJjnwIVGA4p7VpyQUyRIOYwdHJBTJEg5jB0Wx4wKGdnDZxTVL1IbZ-XSjbaTHznmX8rfBWnCPmJmMd&_nc_ohc=Z4p-MgCZnLUQ7kNvgEaxlgm&_nc_oc=Adj34yEQkoq4BVC1rbv3hqxuOEPtGTzHnNq_bsWvPqyo8vgZ9z2tbbWgJ7HSzT1tE3w&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wGK65XEYIiIyewAECv3ua60cvr7cDuW6l98OwR5gTpdRw&oe=67F7AA9D" alt="Dog" class="pet-image">
                <div class="pet-info">
                    <h3 class="pet-name">Ester</h3>
                    <p class="pet-details">Tabby Cat • 2 years old<br>Makati City</p>
                    <span class="pet-status">Available</span>
                </div>
            </div>

            <div class="pet-card">
                <img src="https://scontent.fmnl17-4.fna.fbcdn.net/v/t1.15752-9/483150245_1705309670387494_4346497272094558120_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeG-ha64PWcIpL7sfs8H5cSlZdjzKbK0e0Nl2PMpsrR7Q2L9icW_R_etw4ketgenrAgqATpOxFZC4MDpOtf2gLNX&_nc_ohc=7WJGkcjtJhUQ7kNvgGvkit1&_nc_oc=AdjGtWS8YIv6Vr6pvVHZ9lTg6_gU7lH8mG0W-7Ru3Wew_s331y9hHRNk2sOHMPhA7hw&_nc_zt=23&_nc_ht=scontent.fmnl17-4.fna&oh=03_Q7cD1wEbVmdKkf0WNUf3VpKbYb8w29rAsfon-Dl7sfngD9ev_Q&oe=67F7AE0E" alt="Cat" class="pet-image">
                <div class="pet-info">
                    <h3 class="pet-name">Mickey</h3>
                    <p class="pet-details">chihuahua • 3 year old<br>Quiapo City</p>
                    <span class="pet-status">Available</span>
                </div>
            </div>

            <div class="pet-card">
                <img src="https://scontent.fmnl17-4.fna.fbcdn.net/v/t1.15752-9/483141889_1675833660025267_1290389856524842791_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeFiU2l-lMqiT0yOzqHvaKcBjpUqFd79mTaOlSoV3v2ZNlyoGX5LvyV0WGq8B-1ou-da8iwMWIqMvLc6zVV7ocSO&_nc_ohc=Ig-bidMrjgAQ7kNvgFbwmsH&_nc_oc=AdiWsP5H0Z3Fod7YR9kmeQoIz8fr778iAnJLBmNhxdsqELJJRYvz2uIdxR48AbN7Vkw&_nc_zt=23&_nc_ht=scontent.fmnl17-4.fna&oh=03_Q7cD1wGU_rMD2FIgwYa-HG-mjZwjOaYCt4_tmj0bfU_3bldL1A&oe=67F7BCA9" alt="Dog" class="pet-image">
                <div class="pet-info">
                    <h3 class="pet-name">Fort</h3>
                    <p class="pet-details">Belgian/German • 3 years old<br>Mandaluyong City</p>
                    <span class="pet-status">Available</span>
                </div>
            </div>

            <div class="pet-card">
                <img src="https://scontent.fmnl17-8.fna.fbcdn.net/v/t1.15752-9/481119119_9257386947676660_508132386428156769_n.jpg?_nc_cat=104&ccb=1-7&_nc_sid=0024fc&_nc_eui2=AeHLdCC7IBhD2hxldQWjB2yiK61rtco4kG0rrWu1yjiQbUnjyFKosrdp4eSvhLMjW4Kas7rnVed-HK28qbJ-8M93&_nc_ohc=dqZY7DwxO3MQ7kNvgEkknzy&_nc_oc=AdjnAYeyQgNSdBxSk5A_64MY03bCswdPIU6iSDjXMYpnIHTWP8shHxVE23niIR6lszs&_nc_ad=z-m&_nc_cid=0&_nc_zt=23&_nc_ht=scontent.fmnl17-8.fna&oh=03_Q7cD1wE4ui-qy0cqvC3jTfpOwoH-MwH-8LIVYv5GSbg73VeboQ&oe=67F7CA0B" alt="Cat" class="pet-image">
                <div class="pet-info">
                    <h3 class="pet-name">Sky</h3>
                    <p class="pet-details">Shipoodle • 4 years old<br>Pasig City</p>
                    <span class="pet-status">Available</span>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Pet Details Modal -->
<div id="petDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Pet Details</h2>
            <button class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="pet-gallery">
                <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482958427_546890997878558_7939868464340469320_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEg26tmyKvJjnwIVGA4p7VpyQUyRIOYwdHJBTJEg5jB0Wx4wKGdnDZxTVL1IbZ-XSjbaTHznmX8rfBWnCPmJmMd&_nc_ohc=Z4p-MgCZnLUQ7kNvgEaxlgm&_nc_oc=Adj34yEQkoq4BVC1rbv3hqxuOEPtGTzHnNq_bsWvPqyo8vgZ9z2tbbWgJ7HSzT1tE3w&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wGK65XEYIiIyewAECv3ua60cvr7cDuW6l98OwR5gTpdRw&oe=67F7AA9D" alt="Main pet photo" class="main-image" id="mainImage">
                <div class="thumbnail-grid">
                    <img src="https://scontent.fmnl17-2.fna.fbcdn.net/v/t1.15752-9/481596592_1188427862939538_5723652600303824613_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeGBLyrExbLdDdTzASuJF6NziczxXHFDeI2JzPFccUN4jbX8-GWmxshexj8fLTOn8MEB0l1mnBiqSAj0oKU2VMTQ&_nc_ohc=4l6zYonHqqgQ7kNvgGPA7TT&_nc_oc=Adi0zMNxPg4e893EM5wgYGH2M358oifh1ipsMnTcQcry2B4YiZ0xDmV4yy8Wy3cKQGE&_nc_zt=23&_nc_ht=scontent.fmnl17-2.fna&oh=03_Q7cD1wGu2m2aE4YaCrNXfc-CQFEhi7ZLq-lP2OaIJdyeJ9tOvA&oe=67F7B056" alt="Pet photo 1" class="thumbnail">
                    <img src="https://scontent.fmnl17-7.fna.fbcdn.net/v/t1.15752-9/481462840_1717079572231693_8237407659157347137_n.jpg?_nc_cat=108&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeFtmVaQIC1CAXRI-65-hyHt2AQsx-YdsKTYBCzH5h2wpFeSGJbivQ829m110Sc-WYoS3ZdZIy5X7iaTFpti2_YI&_nc_ohc=bHORPfYSOW8Q7kNvgEg2w_r&_nc_oc=Adi422NPIO2N_lN2j6Rf_0EBd-qnnpAvnwfl04pn_6ujmz5EkJBwuozB9NsyduPu7q0&_nc_zt=23&_nc_ht=scontent.fmnl17-7.fna&oh=03_Q7cD1wGWJTHs6k1jJCVeQZScHscYswFzeF4kn3M3tyIuxDQwZg&oe=67F79B0D" alt="Pet photo 2" class="thumbnail">
                    <img src="https://scontent.fmnl17-5.fna.fbcdn.net/v/t1.15752-9/482814011_3911631112498877_6193875196850877025_n.jpg?_nc_cat=102&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeHtV18A5SMcsSx-v2J3AkZofGazZ_BE_tp8ZrNn8ET-2mnOV9CcAN9h5uCS4vCvLKwZ-LmmBKpYJjiZ-TSg24k1&_nc_ohc=LwpsNuuyxggQ7kNvgGFBOCG&_nc_oc=AdjsApqtrxvCCl5g7tYX5wxX_AHoe8dad9JGGEiRfHmmnM1Y1zla9NxM7GBCLbLgCpA&_nc_zt=23&_nc_ht=scontent.fmnl17-5.fna&oh=03_Q7cD1wGFKBPZILdCc1bEJOf29F1zD7_ShLVMgBtpZm9gUa1_xg&oe=67F79A78" alt="Pet photo 3" class="thumbnail">
                    <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482958427_546890997878558_7939868464340469320_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEg26tmyKvJjnwIVGA4p7VpyQUyRIOYwdHJBTJEg5jB0Wx4wKGdnDZxTVL1IbZ-XSjbaTHznmX8rfBWnCPmJmMd&_nc_ohc=Z4p-MgCZnLUQ7kNvgEaxlgm&_nc_oc=Adj34yEQkoq4BVC1rbv3hqxuOEPtGTzHnNq_bsWvPqyo8vgZ9z2tbbWgJ7HSzT1tE3w&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wGK65XEYIiIyewAECv3ua60cvr7cDuW6l98OwR5gTpdRw&oe=67F7AA9D" alt="Pet photo 4" class="thumbnail">
                </div>
            </div>

            <h2 id="petName" class="pet-name">Ester</h2>
            <div class="pet-details-grid">
                <div class="detail-item">
                    <label>Breed</label>
                    <p id="petBreed">Tabby Cat</p>
                </div>
                <div class="detail-item">
                    <label>Age</label>
                    <p id="petAge">2 years</p>
                </div>
                <div class="detail-item">
                    <label>Gender</label>
                    <p id="petGender">Male</p>
                </div>
                <div class="detail-item">
                    <label>Size</label>
                    <p id="petSize">Large</p>
                </div>
                <div class="detail-item">
                    <label>Weight</label>
                    <p id="petWeight">65 lbs</p>
                </div>
                <div class="detail-item">
                    <label>Status</label>
                    <p id="petStatus">Available for Adoption</p>
                </div>
            </div>

            <div class="pet-description">
                <h3>About Ester</h3>
                <p id="petDescription">
                    Ester is a friendly and energetic Tabby Cat who loves to play fetch and go on long walks.
                    He's great with children and other dogs, and he's looking for an active family who can give him
                    lots of love and attention. Ester is fully vaccinated, neutered, and ready to find his forever home.
                </p>
            </div>

            <div class="shelter-info">
                <h3>Shelter Information</h3>
                <p id="shelterName">Strays Worth Saving</p>
                <p id="shelterAddress">1234 monggo, Mandaluyong City</p>
                <p id="shelterPhone">0912-345-6789</p>
            </div>

            <div class="modal-actions">
                <a href="login.html" class="btn">Apply for Adoption</a>
                <button class="btn" style="background: #f3f4f6; color: #4b5563;">Save to Favorites</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal functionality
    const modal = document.getElementById('petDetailsModal');
    const closeBtn = document.querySelector('.close-btn');
    const petCards = document.querySelectorAll('.pet-card');
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail');

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    petCards.forEach(card => {
        card.addEventListener('click', () => {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });

    closeBtn.addEventListener('click', closeModal);

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Gallery functionality
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', () => {
            mainImage.src = thumbnail.src;
        });
    });
</script>
@endsection