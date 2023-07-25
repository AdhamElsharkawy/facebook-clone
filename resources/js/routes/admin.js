const admin = [
    ////////////////////////////////// admin routes ////////////////////////////
    //dashboard
    {
        path: "dashboard",
        name: "admin.dashboard",
        component: () => import("../pages/admin/dashboard/DashboardPage.vue"),
    },
    // users
    {
        name: "admin.users",
        path: "users",
        component: () => import("../pages/admin/users/index/UserPage.vue"),
        props: true,
    },
    {
        name: "admin.users.create",
        path: "users/add",
        component: () => import("../pages/admin/users/create/CreateUser.vue"),
        props: true,
    },
    {
        name: "admin.users.edit",
        path: "users/edit",
        component: () => import("../pages/admin/users/edit/EditUser.vue"),
        props: true,
    },
    // Departments

    {
        name: "admin.departments",
        path: "departments",
        component: () => import("../pages/admin/departments/index/DepartmentPage.vue"),
        props: true,
    },
    {
        name: "admin.departments.create",
        path: "departments/add",
        component: () => import("../pages/admin/departments/create/CreateDepartment.vue"),
        props: true,
    },
    {
        name: "admin.departments.edit",
        path: "departments/edit",
        component: () => import("../pages/admin/departments/edit/EditDepartment.vue"),
        props: true,
    },
    // Events

    {
        name: "admin.events",
        path: "events",
        component: () => import("../pages/admin/events/index/EventPage.vue"),
        props: true,
    },
    {
        name: "admin.events.create",
        path: "events/add",
        component: () => import("../pages/admin/events/create/CreateEvent.vue"),
        props: true,
    },
    {
        name: "admin.events.edit",
        path: "events/edit",
        component: () => import("../pages/admin/events/edit/EditEvent.vue"),
        props: true,
    },

    // Colleges

    {
        name: "admin.colleges",
        path: "colleges",
        component: () => import("../pages/admin/colleges/index/CollegePage.vue"),
        props: true,
    },
    {
        name: "admin.colleges.create",
        path: "colleges/add",
        component: () => import("../pages/admin/colleges/create/CreateCollege.vue"),
        props: true,
    },
    {
        name: "admin.colleges.edit",
        path: "colleges/edit",
        component: () => import("../pages/admin/colleges/edit/EditCollege.vue"),
        props: true,
    },

    // Companies

    {
        name: "admin.companies",
        path: "companies",
        component: () => import("../pages/admin/companies/index/CompanyPage.vue"),
        props: true,
    },
    {
        name: "admin.companies.create",
        path: "companies/add",
        component: () => import("../pages/admin/companies/create/CreateCompany.vue"),
        props: true,
    },
    {
        name: "admin.companies.edit",
        path: "companies/edit",
        component: () => import("../pages/admin/companies/edit/EditCompany.vue"),
        props: true,
    },

    // educations

    {
        name: "admin.educations",
        path: "educations",
        component: () => import("../pages/admin/educations/index/EducationPage.vue"),
        props: true,
    },
    {
        name: "admin.educations.create",
        path: "educations/add",
        component: () => import("../pages/admin/educations/create/CreateEducation.vue"),
        props: true,
    },
    {
        name: "admin.educations.edit",
        path: "educations/edit",
        component: () => import("../pages/admin/educations/edit/EditEducation.vue"),
        props: true,
    },

    // experiences

    {
        name: "admin.experiences",
        path: "experiences",
        component: () => import("../pages/admin/experiences/index/ExperiencePage.vue"),
        props: true,
    },
    {
        name: "admin.experiences.create",
        path: "experiences/add",
        component: () => import("../pages/admin/experiences/create/CreateExperience.vue"),
        props: true,
    },
    {
        name: "admin.experiences.edit",
        path: "experiences/edit",
        component: () => import("../pages/admin/experiences/edit/EditExperience.vue"),
        props: true,
    },


    // certifications

    {
        name: "admin.certifications",
        path: "certifications",
        component: () => import("../pages/admin/certifications/index/CertificationPage.vue"),
        props: true,
    },
    {
        name: "admin.certifications.create",
        path: "certifications/add",
        component: () => import("../pages/admin/certifications/create/CreateCertification.vue"),
        props: true,
    },
    {
        name: "admin.certifications.edit",
        path: "certifications/edit",
        component: () => import("../pages/admin/certifications/edit/EditCertification.vue"),
        props: true,
    },

    // posts
    {
        name: "admin.posts",
        path: "posts",
        component: () => import("../pages/admin/posts/index/PostPage.vue"),
        props: true,
    },
    {
        name: "admin.posts.edit",
        path: "posts/edit",
        component: () => import("../pages/admin/posts/edit/EditPost.vue"),
        props: true,
    },

    // comments
    {
        name: "admin.comments",
        path: "comments",
        component: () => import("../pages/admin/comments/index/CommentPage.vue"),
        props: true,
    },

    //settings
    {
        name: "admin.settings",
        path: "settings",
        component: () => import("../pages/admin/settings/SettingPage.vue"),
    },
];

export default admin;
