import { useQuery } from '@tanstack/react-query';
import { Button, TitledGreyBox, httpErrorToHuman, toast, useCurrentServerRequired, useExtensionAction, useServerPermission } from '@pterodactyl/sdk';
import { createLink, databasesKey, getDatabases, type ServerDatabase } from './api';
import { isConfigured } from './config';

export default function PhpMyAdmin() {
    const { identifier } = useCurrentServerRequired().attributes;
    const allowed = useServerPermission('database.view_password');
    const databases = useQuery({ queryKey: databasesKey(identifier), queryFn: () => getDatabases(identifier), enabled: allowed && isConfigured() });

    if (!allowed || !isConfigured() || !databases.data?.length) {
        return null;
    }

    return (
        <div className='pma:mx-4 pma:mt-4 pma:max-w-panel pma:sm:mt-6 pma:xl:mx-auto'>
            <TitledGreyBox title='phpMyAdmin'>
                <ul className='pma:grid pma:gap-2'>
                    {databases.data.map((database) => (
                        <Row key={database.id} server={identifier} database={database} />
                    ))}
                </ul>
            </TitledGreyBox>
        </div>
    );
}

function Row({ server, database }: { server: string; database: ServerDatabase }) {
    const open = useExtensionAction('open phpMyAdmin', async () => {
        // Open the window right away so the browser does not block it as a pop-up.
        const target = window.open('about:blank', '_blank');

        try {
            const url = await createLink(server, database.id);

            if (target) {
                target.location.href = url;
            } else {
                window.location.href = url;
            }
        } catch (error) {
            target?.close();
            toast.error(httpErrorToHuman(error));
        }
    });

    return (
        <li className='pma:flex pma:items-center pma:justify-between pma:gap-4'>
            <div className='pma:min-w-0'>
                <p className='pma:truncate pma:text-sm pma:text-foreground'>{database.name}</p>
                <p className='pma:truncate pma:text-xs pma:text-muted-foreground'>
                    {database.username} · {database.host.address}:{database.host.port}
                </p>
            </div>
            <Button size='small' onClick={open}>
                Open in phpMyAdmin
            </Button>
        </li>
    );
}
