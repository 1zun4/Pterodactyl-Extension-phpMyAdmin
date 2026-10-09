import './styles.css';
import { definePterodactylExtension } from '@pterodactyl/sdk';
import { setConfig } from './config';
import PhpMyAdmin from './PhpMyAdmin';

export default definePterodactylExtension({
    setup({ config, slots }) {
        setConfig(config);
        slots.register('server.databases.before', PhpMyAdmin);
    },
});
