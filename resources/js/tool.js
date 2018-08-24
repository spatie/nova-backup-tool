Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'BackupTool',
            path: '/backups',
            component: require('./components/BackupTool'),
        },
    ]);
});
