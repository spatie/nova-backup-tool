Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'BackupTool',
            path: '/BackupTool',
            component: require('./components/Tool'),
        },
    ])
})
