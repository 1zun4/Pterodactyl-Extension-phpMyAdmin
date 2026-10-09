import { http } from '@pterodactyl/sdk';

export interface ServerDatabase {
    id: string;
    name: string;
    username: string;
    host: { address: string; port: number };
}

export const databasesKey = (server: string) => ['phpmyadmin', server, 'databases'] as const;

export async function getDatabases(server: string): Promise<ServerDatabase[]> {
    const { data } = await http.get<{ data: { attributes: ServerDatabase }[] }>(`/api/client/servers/${server}/databases`);

    return data.data.map((item) => item.attributes);
}

export async function createLink(server: string, database: string): Promise<string> {
    const { data } = await http.post<{ url: string }>(`/api/client/servers/${server}/extensions/phpmyadmin/databases/${database}/link`);

    return data.url;
}
