<?php
// ========================================================
// SKILLSPHERE ENTERPRISE DATABASE SEEDER
// database/seed.php
// ========================================================

require_once __DIR__ . '/../db.php';

// Check execution context
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

echo "=== SKILLSPHERE DATABASE SEEDER ===\n";
echo "Connecting to database...\n";

// Disable foreign key checks for clean truncation
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

$tables = [
    'notifications', 'payments', 'certificates', 'reviews', 
    'wishlist', 'lesson_progress', 'enrollments', 'lessons', 
    'courses', 'categories', 'users', 'contact_messages', 'password_resets'
];

foreach ($tables as $table) {
    echo "Truncating table: $table...\n";
    mysqli_query($conn, "TRUNCATE TABLE $table");
}

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
echo "Database tables cleared. Starting data insertion...\n\n";

// ========================================================
// 1. INSERT CATEGORIES
// ========================================================
echo "Inserting course categories...\n";
$categories = [
    ['Web Development', 'web-development'],
    ['Full Stack Development', 'full-stack-development'],
    ['Frontend Engineering', 'frontend-engineering'],
    ['Backend Development', 'backend-development'],
    ['Python Development', 'python-development'],
    ['Java Programming', 'java-programming'],
    ['C++ Programming', 'c-plus-plus-programming'],
    ['JavaScript Mastery', 'javascript-mastery'],
    ['React Development', 'react-development'],
    ['AI & Machine Learning', 'ai-machine-learning'],
    ['Data Science', 'data-science'],
    ['Cybersecurity', 'cybersecurity'],
    ['Cloud Computing', 'cloud-computing'],
    ['UI/UX Design', 'ui-ux-design'],
    ['DevOps', 'devops'],
    ['Mobile App Development', 'mobile-app-development'],
    ['Database Engineering', 'database-engineering'],
    ['System Design', 'system-design'],
    ['Ethical Hacking', 'ethical-hacking']
];

$categoryMap = [];
foreach ($categories as $cat) {
    $stmt = mysqli_prepare($conn, "INSERT INTO categories (category_name, slug) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $cat[0], $cat[1]);
    mysqli_stmt_execute($stmt);
    $categoryMap[$cat[0]] = mysqli_insert_id($conn);
}
echo "Inserted " . count($categoryMap) . " categories.\n";

// ========================================================
// 2. INSERT USERS (Admins, Instructors, Students)
// ========================================================
echo "Inserting users...\n";

// Standard Password Hash ('password')
$passwordHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

$users = [
    // Admins
    [
        'full_name' => 'SkillSphere Admin',
        'username' => 'admin',
        'email' => 'admin@skillsphere.com',
        'password' => $passwordHash,
        'bio' => 'Lead Platform Administrator and Operations Coordinator for SkillSphere E-Learning ecosystem.',
        'role' => 'admin',
        'status' => 'active',
        'phone' => '+1 (555) 019-2834'
    ],
    [
        'full_name' => 'Chief Platform Architect',
        'username' => 'architect',
        'email' => 'architect@skillsphere.com',
        'password' => $passwordHash,
        'bio' => 'Enterprise System Architect and Director of Educational Technologies at SkillSphere.',
        'role' => 'admin',
        'status' => 'active',
        'phone' => '+1 (555) 019-5829'
    ],
    // Instructors
    [
        'full_name' => 'Kalyan Ram',
        'username' => 'kalyan_ram',
        'email' => 'kalyan.r@skillsphere.com',
        'password' => $passwordHash,
        'bio' => 'Full-stack engineering veteran, former Netflix UI Engineer, and React core team contributor. Passionate about clean code and performance scaling.',
        'role' => 'instructor',
        'status' => 'active',
        'phone' => '+91 98480 22338'
    ],
    [
        'full_name' => 'Dr. Sravani Devi',
        'username' => 'sravani_d',
        'email' => 'sravani.d@skillsphere.com',
        'password' => $passwordHash,
        'bio' => 'AI Research Director at MIT with 15+ years experience teaching machine learning, deep learning, and neural network architectures.',
        'role' => 'instructor',
        'status' => 'active',
        'phone' => '+91 94405 66778'
    ],
    [
        'full_name' => 'Venkata Srinivas',
        'username' => 'venkat_s',
        'email' => 'venkat.s@skillsphere.com',
        'password' => $passwordHash,
        'bio' => 'Principal DevOps Architect and Kubernetes Specialist. Helped transition major financial infrastructure systems to cloud-native platforms.',
        'role' => 'instructor',
        'status' => 'active',
        'phone' => '+91 88997 33110'
    ],
    [
        'full_name' => 'Harini Reddy',
        'username' => 'harini_r',
        'email' => 'harini.r@skillsphere.com',
        'password' => $passwordHash,
        'bio' => 'Product Designer and former Design Lead at Stripe. Specialized in design systems, visual hierarchy, usability metrics, and user psychology.',
        'role' => 'instructor',
        'status' => 'active',
        'phone' => '+91 91234 55660'
    ],
    [
        'full_name' => 'Chaitanya Prasad',
        'username' => 'chaitanya_p',
        'email' => 'chaitanya.p@skillsphere.com',
        'password' => $passwordHash,
        'bio' => 'Certified Ethical Hacker, penetration testing advisor, and threat analyst. Consults globally on critical cloud infrastructure defense systems.',
        'role' => 'instructor',
        'status' => 'active',
        'phone' => '+91 98660 11220'
    ],
    [
        'full_name' => 'Lakshmi Prasanna',
        'username' => 'lakshmi_p',
        'email' => 'lakshmi.p@skillsphere.com',
        'password' => $passwordHash,
        'bio' => 'Distinguished database researcher and performance architect. Specialized in distributed database engines, SQL indexing, and gRPC backend systems.',
        'role' => 'instructor',
        'status' => 'active',
        'phone' => '+91 81234 44330'
    ]
];

// Add 25 realistic students
$studentNames = [
    'Ravi Teja Bhupathi', 'Sai Kiran Reddy', 'Murali Krishna Rao', 'Venkateswara Rao', 'Srinivas Naidu', 
    'Sai Teja Goud', 'Kalyan Chakravarthy', 'Prathyusha Reddy', 'Sravanthi Chowdary', 'Madhavi Latha',
    'Keerthi Prasanna', 'Harika Vemuri', 'Ramya Krishna', 'Pavan Kumar Goud', 'Anil Kumar Varma',
    'Karthik Somaraju', 'Deepika Reddy', 'Sushma Swaraj Chowdary', 'Bhargav Ram Goud', 'Taraka Rama Rao',
    'Siddharth Varma', 'Manish Reddy', 'Divya Sree', 'Pranitha Naidu', 'Rakesh Bhupathi'
];

foreach ($studentNames as $idx => $name) {
    $parts = explode(' ', strtolower($name));
    $first = $parts[0];
    $last = $parts[count($parts)-1] ?? 'student';
    $users[] = [
        'full_name' => $name,
        'username' => $first . '_' . $last . '_' . rand(10, 99),
        'email' => $first . '.' . $last . '@example.com',
        'password' => $passwordHash,
        'bio' => "Aspiring developer eager to learn new tech stacks and build responsive products on SkillSphere.",
        'role' => 'student',
        'status' => 'active',
        'phone' => '+91 98499 ' . str_pad($idx + 1000, 5, '0', STR_PAD_LEFT)
    ];
}

$instructorIds = [];
$studentIds = [];
$adminIds = [];

foreach ($users as $u) {
    $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, username, email, password, bio, role, status, phone, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    mysqli_stmt_bind_param($stmt, "ssssssss", $u['full_name'], $u['username'], $u['email'], $u['password'], $u['bio'], $u['role'], $u['status'], $u['phone']);
    mysqli_stmt_execute($stmt);
    
    $uid = mysqli_insert_id($conn);
    if ($u['role'] === 'admin') {
        $adminIds[] = $uid;
    } elseif ($u['role'] === 'instructor') {
        $instructorIds[$u['full_name']] = $uid;
    } else {
        $studentIds[] = $uid;
    }
}
echo "Inserted " . count($adminIds) . " admins, " . count($instructorIds) . " instructors, and " . count($studentIds) . " students.\n";

// Assign custom profile image if it exists in uploads/profiles
$customAvatar = '1779551653_4375.png';
if (file_exists(__DIR__ . '/../uploads/profiles/' . $customAvatar)) {
    mysqli_query($conn, "UPDATE users SET profile_image = '$customAvatar' WHERE role = 'admin'");
    echo "Assigned custom profile image $customAvatar to admin accounts.\n";
}

// ========================================================
// 3. INSERT COURSES
// ========================================================
echo "Inserting courses...\n";

$coursesData = [
    [
        'category' => 'React Development',
        'instructor' => 'Kalyan Ram',
        'title' => 'React 19 & Next.js 15: The Complete Guide',
        'slug' => 'react-19-next-js-15-complete-guide',
        'short' => 'Master React 19, App Router, Server Actions, Suspense, Zustand state management, and build high-performance production SaaS platforms.',
        'desc' => 'Welcome to the most comprehensive course on React 19 and Next.js 15. In this program, you will learn how to build production-grade web applications from scratch. We will explore deep topics including Server Actions, Hydration mechanisms, caching rules, and state management using Zustand. By the end of this course, you will possess a solid grasp of how React schedules rendering, pre-renders HTML, and handles dynamic server elements. We will build a real-world SaaS dashboard complete with analytics and security controls.',
        'level' => 'intermediate',
        'duration' => '38h 12m',
        'price' => 149.99,
        'discount' => 19.99,
        'reqs' => "Basic knowledge of HTML, CSS, and intermediate JavaScript (ES6+).\nNo prior React experience is required.",
        'outcomes' => "Build production-level React 19 and Next.js 15 App Router applications.\nImplement secure database operations with Server Actions.\nDesign interactive components using React Suspense and Error Boundaries.\nDeploy applications with optimal performance and SEO settings on Vercel."
    ],
    [
        'category' => 'System Design',
        'instructor' => 'Kalyan Ram',
        'title' => 'Advanced System Design & Microservices Architecture',
        'slug' => 'advanced-system-design-microservices',
        'short' => 'Learn to architect highly-available, distributed, and scalable systems. Covers load balancing, sharding, caching, and rate limiting.',
        'desc' => 'System design is the foundation of high-scale engineering. This course bridges the gap between coding and systems architecture. We cover database replication models, consistent hashing algorithms, rate limiting patterns, message queues, and API gateways. You will learn the trade-offs of SQL vs NoSQL engines under high-throughput conditions. Each concept is demonstrated through a practical architectural case study, such as designing a global video streaming platform or a real-time messaging pipeline.',
        'level' => 'advanced',
        'duration' => '24h 45m',
        'price' => 199.99,
        'discount' => 29.99,
        'reqs' => "Comfortable with at least one backend language.\nBasic database design understanding (tables, columns, and index concepts).",
        'outcomes' => "Design large-scale database clusters using partitioning and consistent hashing.\nIntegrate Redis and Memcached caching layer strategies to reduce database load.\nHandle asynchronous processes using RabbitMQ and Apache Kafka.\nArchitect resilient service meshes that scale automatically."
    ],
    [
        'category' => 'AI & Machine Learning',
        'instructor' => 'Dr. Sravani Devi',
        'title' => 'Artificial Intelligence & Deep Learning Bootcamp',
        'slug' => 'ai-deep-learning-bootcamp',
        'short' => 'Build and deploy neural networks using Python, TensorFlow, and PyTorch. Covers Computer Vision, NLP, and LLM tuning.',
        'desc' => 'Kickstart your career in Artificial Intelligence. This comprehensive bootcamp takes you from raw mathematical theory directly to training machine learning algorithms. We cover linear regression, decision trees, support vector machines, and convolutional neural networks (CNNs). We will train neural network layers to recognize patterns, translate languages, and predict trends. The course also details fine-tuning large language models (LLMs) using HuggingFace API.',
        'level' => 'beginner',
        'duration' => '48h 30m',
        'price' => 189.99,
        'discount' => 24.99,
        'reqs' => "Basic programming logic in Python.\nBasic high school algebra and statistics concepts.",
        'outcomes' => "Train robust deep learning models using PyTorch and TensorFlow.\nBuild image classifiers and text analysis models from scratch.\nEvaluate algorithm efficiency using validation sets, ROC curves, and precision-recall metrics.\nDeploy ML models as REST API endpoints for software integrations."
    ],
    [
        'category' => 'Ethical Hacking',
        'instructor' => 'Chaitanya Prasad',
        'title' => 'Ethical Hacking: Network Penetration Testing Masterclass',
        'slug' => 'ethical-hacking-network-pen-testing',
        'short' => 'Learn penetration testing and white-hat hacking. Covers scanning, exploiting, SQL injection, and buffer overflows.',
        'desc' => 'Securing systems requires understanding the tools and techniques used by threat actors. This ethical hacking masterclass walks you through setting up a secure virtual laboratory, mapping networks, scanning for open vulnerabilities, and deploying payloads. We cover web security challenges, password cracking, SQL injection defense, and cross-site scripting (XSS) remediation. All modules adhere strictly to professional white-hat compliance standards.',
        'level' => 'intermediate',
        'duration' => '32h 15m',
        'price' => 129.99,
        'discount' => 19.99,
        'reqs' => "Understanding of basic network protocols (TCP/IP, HTTP, DNS).\nBasic command line experience in Linux or Windows.",
        'outcomes' => "Perform comprehensive vulnerability assessments on enterprise networks.\nDeploy Metasploit modules to identify weak authorization nodes.\nSecure codebases against major OWASP Top 10 vulnerabilities.\nWrite detailed, professional penetration testing reports for security audits."
    ],
    [
        'category' => 'DevOps',
        'instructor' => 'Venkata Srinivas',
        'title' => 'Docker, Kubernetes & AWS: Ultimate DevOps Bootcamp',
        'slug' => 'docker-kubernetes-aws-devops',
        'short' => 'Automate your deployments with Docker containers, Kubernetes orchestration, and AWS infrastructure pipelines.',
        'desc' => ' DevOps is a culture and set of tools that allow teams to build, test, and release software faster. This practical program covers setting up Docker files, deploying local pods in Kubernetes, and scaling microservices on Amazon Web Services (AWS) using EKS. You will learn to write continuous integration and deployment (CI/CD) pipelines using GitHub Actions, ensuring code commits deploy safely and automatically.',
        'level' => 'advanced',
        'duration' => '40h 50m',
        'price' => 159.99,
        'discount' => 21.99,
        'reqs' => "Familiarity with Git commands.\nBasic Linux shell navigation skills.",
        'outcomes' => "Containerize complex multi-service applications using Docker.\nOrchestrate high-availability Kubernetes clusters with self-healing pods.\nConfigure automated CI/CD deployment pipelines to AWS EKS.\nMonitor production health using Prometheus, Grafana, and cloud logs."
    ],
    [
        'category' => 'UI/UX Design',
        'instructor' => 'Harini Reddy',
        'title' => 'UI/UX Design Systems: Designing Premium SaaS Platforms',
        'slug' => 'ui-ux-design-systems-premium-saas',
        'short' => 'Create beautiful Figma design systems, wireframes, and high-fidelity mockups following Apple and Stripe layout styles.',
        'desc' => 'Great product experiences begin with design. This course covers Figma styling rules, dynamic components, layout grids, auto-layout parameters, and prototyping animations. We study visual principles, HSL color schemes, dark-mode design, typography pairing, and accessibility rules. You will learn to draft UX research files and translate user request blueprints into interactive, state-of-the-art visual assets.',
        'level' => 'beginner',
        'duration' => '18h 20m',
        'price' => 99.99,
        'discount' => 15.99,
        'reqs' => "No design software experience required.\nFigma account (free tier is sufficient).",
        'outcomes' => "Build comprehensive, reusable Figma design systems with components and variants.\nApply high-end user research frameworks to solve complex navigation issues.\nDraft responsive wireframes and premium high-fidelity UI screens.\nDeliver polished layouts with high-quality visual hierarchy and typography."
    ],
    [
        'category' => 'Database Engineering',
        'instructor' => 'Lakshmi Prasanna',
        'title' => 'High-Performance Database Engineering & Scaling',
        'slug' => 'high-performance-database-engineering',
        'short' => 'Optimize queries, build advanced indexes, configure replication, and master transaction isolation levels.',
        'desc' => 'Database engines are the bottleneck of almost every high-scale system. This course takes you under the hood of relational systems (MySQL, PostgreSQL). We learn about B-Trees, transaction concurrency, locking mechanisms, explain plans, and vacuuming. We cover sharding techniques and high-availability database setups. You will learn how write-ahead logs work and how to configure databases for optimal read/write throughput.',
        'level' => 'advanced',
        'duration' => '26h 10m',
        'price' => 139.99,
        'discount' => 19.99,
        'reqs' => "Familiarity with writing basic SQL queries (SELECT, JOIN, INSERT).\nBasic understanding of relational database models.",
        'outcomes' => "Analyze query performance and design highly optimized composite indexes.\nImplement master-slave replication models to scale database read capacity.\nManage concurrent transactions without deadlocks using proper isolation levels.\nOptimize query execution plans and database configuration parameters."
    ],
    [
        'category' => 'Data Science',
        'instructor' => 'Dr. Sravani Devi',
        'title' => 'Python for Data Science & Predictive Analytics',
        'slug' => 'python-data-science-analytics',
        'short' => 'Analyze large datasets, perform statistical models, and build predictive machine learning systems with Pandas and NumPy.',
        'desc' => 'Unlock the value of data using Python. In this course, you will master scientific libraries like NumPy, Pandas, Matplotlib, Seaborn, and Scikit-Learn. We cover data wrangling, handling missing values, statistical data modeling, hypothesis testing, and building regression and classification models. We focus on real-world datasets, such as e-commerce user actions and financial market trends, to gain actionable insights.',
        'level' => 'intermediate',
        'duration' => '35h 40m',
        'price' => 119.99,
        'discount' => 17.99,
        'reqs' => "Basic Python programming logic.\nHigh school level math skills.",
        'outcomes' => "Clean, reshape, and filter massive data collections with Pandas.\nVisualize statistics through high-quality graphics and interactive plots.\nBuild predictive models using supervised machine learning algorithms.\nPresent actionable business insights using statistical validation."
    ],
    [
        'category' => 'Backend Development',
        'instructor' => 'Lakshmi Prasanna',
        'title' => 'Advanced Backend Development with Go & gRPC',
        'slug' => 'advanced-backend-go-grpc',
        'short' => 'Build high-concurrency microservices in Go, master channels and goroutines, and deploy gRPC APIs.',
        'desc' => 'Go is the language of modern backend infrastructure. This program explores concurrent programming using goroutines, channels, and waitgroups. We design microservices that communicate using protocol buffers (Protobuf) over HTTP/2 with gRPC. You will build clean backend systems, configure authentication middleware, write unit tests, and structure backends for high throughput and low resource utilization.',
        'level' => 'advanced',
        'duration' => '28h 15m',
        'price' => 169.99,
        'discount' => 24.99,
        'reqs' => "Familiarity with basic programming loops, variables, and arrays.\nPrior Go programming experience is helpful but not strictly required.",
        'outcomes' => "Write high-throughput concurrent software using Go concurrency primitives.\nDesign and implement highly efficient gRPC APIs using Protocol Buffers.\nIntegrate PostgreSQL databases using sqlx and clean database pooling.\nDockerize backend services and deploy with container monitoring tools."
    ],
    [
        'category' => 'JavaScript Mastery',
        'instructor' => 'Kalyan Ram',
        'title' => 'Modern JavaScript (ES6+): The Complete Mastery Course',
        'slug' => 'modern-javascript-es6-complete-mastery',
        'short' => 'Deep dive into asynchronous JavaScript, promises, prototypes, event loops, scopes, closure, and ES2024 features.',
        'desc' => 'Take your JavaScript skills to the professional level. This course demystifies the engine behaviors under the hood: the event loop, execution contexts, variable hoisting, lexical closures, prototypal inheritance, and memory management. We explore advanced modern features like async/await, generators, symbols, proxies, and module loading. By understanding the core runtime details, you will write cleaner, bug-free, and faster JavaScript applications.',
        'level' => 'beginner',
        'duration' => '22h 30m',
        'price' => 89.99,
        'discount' => 12.99,
        'reqs' => "Basic understanding of computer files and folders.\nNo programming experience is required.",
        'outcomes' => "Master async control flows, promises, and network fetch states.\nWrite modular, reusable JavaScript patterns using classes and modules.\nDebug scope issues, closure side-effects, and binding contexts (this key).\nBuild interactive frontends using DOM APIs and event delegation."
    ]
];

$courseMap = [];
foreach ($coursesData as $c) {
    $catId = $categoryMap[$c['category']];
    $instId = $instructorIds[$c['instructor']];
    
    $thumbDir = __DIR__ . '/../uploads/thumbnails';
    $fileName = $c['slug'] . '.svg';
    foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
        if (file_exists($thumbDir . '/' . $c['slug'] . '.' . $ext)) {
            $fileName = $c['slug'] . '.' . $ext;
            break;
        }
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO courses (category_id, instructor_id, title, slug, short_description, description, level, duration, price, discount_price, requirements, learning_outcomes, status, total_students, average_rating, thumbnail) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', ?, ?, ?)");
    
    $rating = rand(450, 495) / 100.0;
    $students = rand(350, 4200);
    
    mysqli_stmt_bind_param($stmt, "iissssssddssdis", 
        $catId, $instId, $c['title'], $c['slug'], $c['short'], $c['desc'], 
        $c['level'], $c['duration'], $c['price'], $c['discount'], 
        $c['reqs'], $c['outcomes'], $students, $rating, $fileName
    );
    
    mysqli_stmt_execute($stmt);
    $courseMap[$c['title']] = mysqli_insert_id($conn);
}
echo "Inserted " . count($courseMap) . " courses.\n";

// ========================================================
// 4. INSERT LESSONS
// ========================================================
echo "Inserting lessons...\n";

$lessonsData = [
    'React 19 & Next.js 15: The Complete Guide' => [
        ['Welcome & Course Overview', '10:15', 'Introduction to the course scope, project setups, and syllabus. We overview the application we will build.', 1],
        ['React 19 Core Upgrades', '18:45', 'Detailed look at new hooks like useActionState, useOptimistic, useFormStatus, and compilation upgrades.', 1],
        ['App Router Fundamentals', '25:30', 'Exploration of page and layout systems, routing paradigms, and folder structure setups.', 0],
        ['React Server Components (RSC)', '32:10', 'Under the hood of server and client components. We discuss compilation phases and bundle optimizations.', 0],
        ['Server Actions Deep Dive', '22:15', 'Building forms, validating fields with Zod, and updating database values directly through secure Server Actions.', 0],
        ['State Management with Zustand', '18:50', 'Setting up lightweight client stores, handling async state updates, and syncing client states with React Server Components.', 0],
        ['Deployment & Vercel Optimization', '15:20', 'Production build configurations, setting environment secrets, and deploying onto Vercel infrastructure.', 0]
    ],
    'Advanced System Design & Microservices Architecture' => [
        ['System Design Basics', '12:40', 'Understand availability (SLA), durability, single points of failure, and scalability concepts.', 1],
        ['Load Balancers & Reverse Proxies', '22:10', 'How NGINX, HAProxy, and AWS ALB route client requests and manage network traffic load balancing.', 1],
        ['Sharding & Consistent Hashing', '30:15', 'Partitioning databases, handling hotkeys, and implementing consistent hashing routing rings.', 0],
        ['Caching Strategies (Redis)', '25:50', 'Cache-aside, write-through, and read-through caching configurations. We configure Redis eviction options.', 0],
        ['Message Queues & Event Streaming', '28:30', 'Decoupling services using RabbitMQ and processing real-time telemetry streams using Apache Kafka topics.', 0],
        ['Designing a Distributed System', '35:20', 'Step-by-step case study architecting a highly available global chat platform supporting millions of concurrent clients.', 0]
    ],
    'Artificial Intelligence & Deep Learning Bootcamp' => [
        ['Math Foundations for AI', '20:15', 'Reviewing core linear algebra, matrices, partial derivatives, and statistics foundations.', 1],
        ['Introduction to Python & NumPy', '18:30', 'Building array structures, performing matrix computations, and understanding vector operations in NumPy.', 1],
        ['Supervised Machine Learning', '28:40', 'Working with linear regression, logistics models, random forest trees, and K-Nearest Neighbors.', 0],
        ['Neural Networks from Scratch', '35:50', 'How neural networks compute weights. Implementing activation algorithms (ReLU, Sigmoid) and backpropagation.', 0],
        ['Computer Vision with CNNs', '32:10', 'Designing Convolutional Neural Networks to filter, resize, and classify image structures in PyTorch.', 0],
        ['Natural Language Processing (NLP)', '26:40', 'Tokenizing text patterns, vector embedding models, and understanding Recurrent Neural Network layers.', 0]
    ],
    'Ethical Hacking: Network Penetration Testing Masterclass' => [
        ['Course Overview & Safety Rules', '10:05', 'Establishing lab rules and outlining safety, legality, and compliance boundaries of white-hat audits.', 1],
        ['Setting Up Kalilinux Virtual Lab', '15:30', 'Installing virtualization tools (VirtualBox/VMware), configuring Kali Linux ISO, and securing proxy networks.', 1],
        ['Reconnaissance & Network Port Scanning', '24:20', 'Mapping nodes, scanning open ports using Nmap, and querying active service banners safely.', 0],
        ['Exploiting Network Vulnerabilities', '28:10', 'Locating targets using Metasploit, configuring payloads, and simulating ethical access overrides.', 0],
        ['SQL Injection (SQLi) Exploits', '26:50', 'Understanding server-side query construction, extracting data tables, and auditing parameterized inputs.', 0],
        ['Cross-Site Scripting (XSS) Auditing', '20:40', 'Discovering stored, reflected, and DOM-based scripting vulnerabilities on simulated targets.', 0]
    ],
    'Docker, Kubernetes & AWS: Ultimate DevOps Bootcamp' => [
        ['DevOps Philosophy & Core Tools', '12:15', 'Understanding continuous integration, deployments, infra-as-code, and logging metrics.', 1],
        ['Docker Containers from Scratch', '22:40', 'Writing Dockerfiles, managing container image layers, linking ports, and deploying with Docker Compose.', 1],
        ['Introduction to Kubernetes', '26:10', 'Mastering pods, services, deployment templates, configmaps, and cluster architecture.', 0],
        ['Scaling Pods & ReplicaSets', '20:50', 'Automating self-healing service scaling using horizontal pod autoscalers (HPA) and replication.', 0],
        ['Configuring AWS Elastic Kubernetes Service', '35:20', 'Deploying cloud clusters, mounting storage volumes, and securing AWS EKS IAM authorization.', 0],
        ['Automated CI/CD Pipelines', '28:10', 'Building testing and delivery triggers using GitHub Actions to push Docker images directly to AWS.', 0]
    ],
    'UI/UX Design Systems: Designing Premium SaaS Platforms' => [
        ['UI/UX Fundamentals & Visual Design', '15:40', 'The role of alignment, contrast, visual hierarchy, clean spacing, and accessibility guidelines.', 1],
        ['Getting Started with Figma', '18:20', 'Exploring Figma interface controls, framing containers, layout structures, and design grids.', 1],
        ['Auto Layout & Responsive Design', '25:10', 'Creating flexible layouts using Auto-Layout variables that adjust to different screen widths.', 0],
        ['Building Reusable Components', '28:40', 'Designing modular buttons, forms, headers, and dashboard widgets with variant properties.', 0],
        ['Interactive Prototyping & Motion', '22:15', 'Linking screens, building smooth modal transitions, and setting up micro-interaction physics.', 0],
        ['Documenting Design Systems', '16:50', 'Drafting component guidelines, grid spacing manuals, and font hierarchies for developer handoff.', 0]
    ],
    'High-Performance Database Engineering & Scaling' => [
        ['Database Storage Engines', '18:10', 'Deep dive into InnoDB structures, page allocations, B-Tree leaf layouts, and memory buffering.', 1],
        ['Advanced Indexing Techniques', '25:40', 'Designing composite indexes, covering indexes, and avoiding index scans under heavy query loads.', 1],
        ['Transaction Concurrency Control', '22:50', 'How databases lock rows, managing read phenomena (phantom reads), and transaction isolation states.', 0],
        ['Query Execution Plans', '24:20', 'Reading SQL output explanations, identifying nested loops, sorting costs, and query bottlenecks.', 0],
        ['Master-Slave Replication Configurations', '30:30', 'Structuring replication clusters, managing async vs sync lags, and routing read requests.', 0],
        ['Database Partitioning & Sharding', '28:10', 'Horizontal data splits, shard routing mechanisms, and cross-shard queries optimization.', 0]
    ],
    'Python for Data Science & Predictive Analytics' => [
        ['Scientific Computing with NumPy', '16:20', 'Working with dynamic multidimensional arrays, math operations, and statistics tools in NumPy.', 1],
        ['Data Analysis with Pandas', '25:40', 'Reading dataset files, cleaning rows, reshaping dataframes, and performing groupby aggregates.', 1],
        ['Data Visualization (Matplotlib & Seaborn)', '20:10', 'Building line graphs, scatter plots, frequency histograms, and heatmaps from raw datasets.', 0],
        ['Statistical Modeling & Testing', '24:50', 'Hypothesis testing, calculating z-scores, p-values, and conducting correlation metrics.', 0],
        ['Supervised Machine Learning Systems', '32:30', 'Training model pipelines using Scikit-Learn to categorize metrics and predict values.', 0],
        ['Feature Engineering & Model Tuning', '26:15', 'Scale variables, encode labels, impute missing parameters, and optimize grid parameters.', 0]
    ],
    'Advanced Backend Development with Go & gRPC' => [
        ['Go Concurrency Fundamentals', '15:50', 'Understanding the Go scheduler, launching goroutines, and building waitgroup barriers.', 1],
        ['Channels & Asynchronous Pipelines', '22:40', 'Communicating safely between threads using buffered channels and select multiplexing.', 1],
        ['Introduction to Protocol Buffers', '18:10', 'Writing schema files, compiling structs, and handling binary serializations.', 0],
        ['Building gRPC Servers in Go', '26:50', 'Implementing unary and streaming gRPC service handlers on Go network listeners.', 0],
        ['Database Connection Pooling', '24:30', 'Structuring SQL transaction bounds, managing connection pools, and query integrations.', 0],
        ['Microservice Deployment with Docker', '20:10', 'Building multi-stage minimal Docker images for fast Go application deployments.', 0]
    ],
    'Modern JavaScript (ES6+): The Complete Mastery Course' => [
        ['Execution Context & Event Loop', '14:20', 'Inside the engine: call stacks, memory heaps, task queues, and asynchronous event loops.', 1],
        ['Scope, Closure, and Dynamic Binding', '18:40', 'Lexical scoping boundaries, function closures, and overriding bind contexts (call, apply, bind).', 1],
        ['Prototypes & Object Inheritance', '20:15', 'Prototypal chains, class patterns, instantiating models, and class extensions.', 0],
        ['Asynchronous Flow (Promises & Async/Await)', '25:30', 'Managing HTTP fetch state arrays, building promises, and asynchronous catch exceptions.', 0],
        ['Modern ES6+ Utility Features', '18:10', 'Destructuring data arrays, spread parameters, dynamic import files, and ES2024 updates.', 0],
        ['DOM API Integration & Delegation', '22:50', 'Handling page elements, event bubble tracking, and building custom single page router tools.', 0]
    ]
];

$lessonCount = 0;
foreach ($lessonsData as $courseTitle => $lessons) {
    if (!isset($courseMap[$courseTitle])) continue;
    $cid = $courseMap[$courseTitle];
    
    foreach ($lessons as $l) {
        $stmt = mysqli_prepare($conn, "INSERT INTO lessons (course_id, title, duration, lesson_content, lesson_order, is_preview) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isssii", $cid, $l[0], $l[1], $l[2], $l[3], $l[3]);
        mysqli_stmt_execute($stmt);
        $lessonCount++;
    }
}
echo "Inserted $lessonCount lessons.\n";

// ========================================================
// 5. INSERT ENROLLMENTS, PAYMENTS, AND PROGRESS
// ========================================================
echo "Simulating student activity (enrollments, payments, progress)...\n";

$allCourses = fetchAll("SELECT id, price, discount_price FROM courses");
$allLessons = fetchAll("SELECT id, course_id FROM lessons");

$lessonsByCourse = [];
foreach ($allLessons as $ls) {
    $lessonsByCourse[$ls['course_id']][] = $ls['id'];
}

$enrollmentCount = 0;
$paymentCount = 0;
$progressCount = 0;
$reviewCount = 0;

$paymentMethods = ['Stripe Credit Card', 'PayPal Secure', 'Apple Pay', 'Google Pay'];

// Generate reviews text bank
$reviewsBank = [
    5 => [
        "This course is absolutely fantastic! The content is extremely clean and presented logically.",
        "A game-changer for my career. The projects we built helped me land a new software engineer role.",
        "Instructor is top-notch. Explanation is clear, and the production-level code examples are premium.",
        "Best resource on this subject on the web. Highly recommended for developers looking to scale.",
        "Very engaging and clear. The modular pacing made learning advanced architecture feel approachable."
    ],
    4 => [
        "Great coverage of all features, though a couple of sections went a bit too fast for beginners.",
        "Highly informative and comprehensive. Excellent production environment walkthrough.",
        "Very good course with great real-world examples. I learned a lot of practical techniques.",
        "Solid content. Sometimes the database queries could use a bit more performance metrics."
    ],
    3 => [
        "Decent course, but it covers a lot of topics that are already well-documented.",
        "Useful information, but the audio levels vary slightly across video modules."
    ]
];

foreach ($studentIds as $studentId) {
    // Each student enrolls in 2 to 5 random courses
    $enrollCount = rand(2, 5);
    $selectedCourses = array_rand($allCourses, $enrollCount);
    if (!is_array($selectedCourses)) {
        $selectedCourses = [$selectedCourses];
    }
    
    foreach ($selectedCourses as $cIdx) {
        $course = $allCourses[$cIdx];
        $courseId = $course['id'];
        $price = ($course['discount_price'] > 0) ? $course['discount_price'] : $course['price'];
        
        // Enroll (Use FLOOR(RAND() * 150) instead of rand(1, 150) for MySQL compliance)
        $stmt = mysqli_prepare($conn, "INSERT INTO enrollments (user_id, course_id, payment_status, enrolled_at) VALUES (?, ?, 'paid', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 150) DAY))");
        mysqli_stmt_bind_param($stmt, "ii", $studentId, $courseId);
        $res = mysqli_stmt_execute($stmt);
        
        if ($res) {
            $enrollId = mysqli_insert_id($conn);
            $enrollmentCount++;
            
            // Payment record
            $method = $paymentMethods[array_rand($paymentMethods)];
            $txId = "txn_" . bin2hex(random_bytes(8));
            $stmt = mysqli_prepare($conn, "INSERT INTO payments (user_id, course_id, amount, payment_method, transaction_id, payment_status, paid_at) VALUES (?, ?, ?, ?, ?, 'success', NOW())");
            mysqli_stmt_bind_param($stmt, "iidss", $studentId, $courseId, $price, $method, $txId);
            mysqli_stmt_execute($stmt);
            $paymentCount++;
            
            // Simulating Lesson Progress
            if (isset($lessonsByCourse[$courseId])) {
                $lessons = $lessonsByCourse[$courseId];
                // Student completes a random subset of lessons (e.g. 20% to 100%)
                $completeRatio = rand(2, 10);
                $completeCount = ceil(count($lessons) * ($completeRatio / 10));
                
                $completedLessons = (array)array_rand($lessons, $completeCount);
                
                foreach ($completedLessons as $lIdx) {
                    $lessonId = $lessons[$lIdx];
                    // Corrected MySQL RAND syntax to FLOOR(300 + RAND() * 1500)
                    $stmt = mysqli_prepare($conn, "INSERT INTO lesson_progress (user_id, lesson_id, completed, watched_time, completed_at) VALUES (?, ?, 1, FLOOR(300 + RAND() * 1500), NOW())");
                    mysqli_stmt_bind_param($stmt, "ii", $studentId, $lessonId);
                    mysqli_stmt_execute($stmt);
                    $progressCount++;
                }
                
                // If 100% completed, issue certificate
                if ($completeRatio === 10) {
                    $certCode = "CERT-" . strtoupper(bin2hex(random_bytes(5)));
                    $stmt = mysqli_prepare($conn, "INSERT INTO certificates (user_id, course_id, certificate_code, issued_at) VALUES (?, ?, ?, NOW())");
                    mysqli_stmt_bind_param($stmt, "iis", $studentId, $courseId, $certCode);
                    mysqli_stmt_execute($stmt);
                    
                    // Mark enrollment completed
                    $stmt = mysqli_prepare($conn, "UPDATE enrollments SET completed_at = NOW() WHERE user_id = ? AND course_id = ?");
                    mysqli_stmt_bind_param($stmt, "ii", $studentId, $courseId);
                    mysqli_stmt_execute($stmt);
                }
            }
            
            // Add a review
            $rating = rand(3, 5);
            if ($rating === 3 && rand(0, 1) === 0) $rating = 4; // bias to higher ratings
            $reviewText = $reviewsBank[$rating][array_rand($reviewsBank[$rating])];
            
            // Use FLOOR(RAND() * 60) for MySQL compliance
            $stmt = mysqli_prepare($conn, "INSERT INTO reviews (user_id, course_id, rating, review, created_at) VALUES (?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 60) DAY))");
            mysqli_stmt_bind_param($stmt, "iiis", $studentId, $courseId, $rating, $reviewText);
            mysqli_stmt_execute($stmt);
            $reviewCount++;
        }
    }
}

echo "Simulated $enrollmentCount enrollments, $paymentCount payments, $progressCount lesson progress checkouts, and $reviewCount course reviews.\n";

// ========================================================
// 6. NOTIFICATIONS AND CONTACT MESSAGES
// ========================================================
echo "Adding platform notifications and messages...\n";

// System announcement notification for all students
$allStudentUserIds = fetchAll("SELECT id FROM users WHERE role = 'student'");
foreach ($allStudentUserIds as $stud) {
    $title = "Welcome to SkillSphere Premium!";
    $message = "Explore our newly updated industry-standard bootcamps in AI, DevOps, React 19, Go Backend, and System Design. Unlock your learning certificate upon completion!";
    $stmt = mysqli_prepare($conn, "INSERT INTO notifications (user_id, title, message, is_read) VALUES (?, ?, ?, 0)");
    mysqli_stmt_bind_param($stmt, "iss", $stud['id'], $title, $message);
    mysqli_stmt_execute($stmt);
}

// Add some contact messages
$contactMsgs = [
    ['Kiran Kumar Chowdary', 'kiran.c@example.com', 'Enterprise Training Programs', 'Hello, do you offer corporate discount packages for engineering teams of 20+ members looking to enroll in the system design courses?'],
    ['Swapna Reddy', 'swapna.r@example.com', 'Instructor Onboarding Query', 'I am a Lead Security Architect. I would love to contribute a course on Advanced Threat Mitigation. What is your instructor payout structure?'],
    ['Nikhil Goud', 'nikhil.g@example.com', 'Certificate Verification', 'I completed the Docker DevOps program. How can I share my certificate directly on LinkedIn profiles?']
];

foreach ($contactMsgs as $msg) {
    $stmt = mysqli_prepare($conn, "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $msg[0], $msg[1], $msg[2], $msg[3]);
    mysqli_stmt_execute($stmt);
}

// ========================================================
// 7. SYNC PLATFORM STATISTICS & AVERAGE RATINGS
// ========================================================
echo "Syncing course aggregates (total students, average ratings)...\n";

$courses = fetchAll("SELECT id FROM courses");
foreach ($courses as $c) {
    $cid = $c['id'];
    
    // Count enrolled
    $enroll = fetchSingleSecure("SELECT COUNT(*) AS total FROM enrollments WHERE course_id = ?", [$cid]);
    $totalEnrolled = $enroll['total'] ?? 0;
    
    // Avg rating
    $rev = fetchSingleSecure("SELECT AVG(rating) AS avg_rate FROM reviews WHERE course_id = ?", [$cid]);
    $avgRating = $rev['avg_rate'] ?? 0;
    
    if (empty($avgRating) || $avgRating == 0) {
        $avgRating = rand(450, 490) / 100.0;
    }
    
    $stmt = mysqli_prepare($conn, "UPDATE courses SET total_students = ?, average_rating = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "idi", $totalEnrolled, $avgRating, $cid);
    mysqli_stmt_execute($stmt);
}

echo "\nDatabase seeding completed successfully!\n";
echo "You can now log in using the credentials:\n";
echo "- Admin: admin@skillsphere.com / password\n";
echo "- Instructor: kalyan.r@skillsphere.com / password\n";
echo "- Students: ravi.bhupathi@example.com, sai.reddy@example.com / password\n";
echo "========================================================\n";
?>
