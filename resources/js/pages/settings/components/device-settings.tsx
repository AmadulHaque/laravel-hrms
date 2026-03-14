import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { SettingsSection } from '@/components/settings-section';
import { CrudTable } from '@/components/CrudTable';
import { CrudFormModal } from '@/components/CrudFormModal';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from '@/components/custom-toast';

interface Device {
  id: number;
  name: string;
  serial_number?: string | null;
  area_id?: string | null;
  device_ip?: string | null;
  status: number;
  heartbeat_status: boolean;
}

interface DeviceSettingsProps {
  devices?: Device[];
}

export default function DeviceSettings({ devices = [] }: DeviceSettingsProps) {
  const { t } = useTranslation();
  const { auth = {} } = usePage().props as any;
  const permissions = auth?.permissions || [];
  const canManage = permissions.includes('manage-biomatric-attedance-settings');

  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [currentItem, setCurrentItem] = useState<Device | null>(null);
  const [formMode, setFormMode] = useState<'create' | 'edit'>('create');

  const handleAction = (action: string, item: Device) => {
    setCurrentItem(item);

    if (action === 'edit') {
      setFormMode('edit');
      setIsFormModalOpen(true);
    }

    if (action === 'delete') {
      setIsDeleteModalOpen(true);
    }
  };

  const handleAddNew = () => {
    setCurrentItem(null);
    setFormMode('create');
    setIsFormModalOpen(true);
  };

  const handleFormSubmit = (formData: any) => {
    const payload = {
      name: formData.name,
      serial_number: formData.serial_number || null,
      area_id: formData.area_id || null,
      device_ip: formData.device_ip || null,
      status: Number(formData.status),
    };

    if (formMode === 'create') {
      router.post(route('settings.zekto.devices.store'), payload, {
        preserveScroll: true,
        onSuccess: (page: any) => {
          setIsFormModalOpen(false);
          if (page.props.flash?.success) {
            toast.success(t(page.props.flash.success));
          } else if (page.props.flash?.error) {
            toast.error(t(page.props.flash.error));
          }
        },
        onError: (errors) => {
          toast.error(t('Failed to create device: {{errors}}', { errors: Object.values(errors).join(', ') }));
        }
      });

      return;
    }

    if (!currentItem) {
      return;
    }

    router.put(route('settings.zekto.devices.update', currentItem.id), payload, {
      preserveScroll: true,
      onSuccess: (page: any) => {
        setIsFormModalOpen(false);
        if (page.props.flash?.success) {
          toast.success(t(page.props.flash.success));
        } else if (page.props.flash?.error) {
          toast.error(t(page.props.flash.error));
        }
      },
      onError: (errors) => {
        toast.error(t('Failed to update device: {{errors}}', { errors: Object.values(errors).join(', ') }));
      }
    });
  };

  const handleDeleteConfirm = () => {
    if (!currentItem) {
      return;
    }

    router.delete(route('settings.zekto.devices.destroy', currentItem.id), {
      preserveScroll: true,
      onSuccess: (page: any) => {
        setIsDeleteModalOpen(false);
        if (page.props.flash?.success) {
          toast.success(t(page.props.flash.success));
        } else if (page.props.flash?.error) {
          toast.error(t(page.props.flash.error));
        }
      },
      onError: (errors) => {
        toast.error(t('Failed to delete device: {{errors}}', { errors: Object.values(errors).join(', ') }));
      }
    });
  };

  return (
    <SettingsSection
      title={t('Device Settings')}
      description={t('Manage ZKTeco devices for biometric attendance sync')}
      action={
        <Button onClick={handleAddNew} disabled={!canManage} size="sm">
          <Plus className="h-4 w-4 mr-2" />
          {t('Add Device')}
        </Button>
      }
    >
      <Card>
        <CardContent className="p-0">
          <div className="max-h-96 overflow-y-auto">
            <CrudTable
              columns={[
                {
                  key: 'name',
                  label: t('Name'),
                  sortable: true,
                },
                {
                  key: 'serial_number',
                  label: t('Serial Number'),
                  sortable: true,
                },
                {
                  key: 'device_ip',
                  label: t('Device IP'),
                  sortable: true,
                  render: (value) => <span className="font-mono">{value || '-'}</span>
                },
                {
                  key: 'area_id',
                  label: t('Area ID'),
                  sortable: true,
                },
                {
                  key: 'heartbeat_status',
                  label: t('Heartbeat'),
                  sortable: false,
                  render: (value) => (
                    <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                      {value ? t('Online') : t('Offline')}
                    </span>
                  )
                },
                {
                  key: 'status',
                  label: t('Status'),
                  sortable: false,
                  render: (value) => (
                    <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${Number(value) === 1 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}`}>
                      {Number(value) === 1 ? t('Active') : t('Inactive')}
                    </span>
                  )
                }
              ]}
              actions={[
                {
                  label: t('Edit'),
                  icon: 'Edit',
                  action: 'edit',
                  className: 'text-amber-500',
                  requiredPermission: 'manage-biomatric-attedance-settings'
                },
                {
                  label: t('Delete'),
                  icon: 'Trash2',
                  action: 'delete',
                  className: 'text-red-500',
                  requiredPermission: 'manage-biomatric-attedance-settings'
                }
              ]}
              data={devices}
              from={1}
              onAction={handleAction}
              permissions={permissions}
              entityPermissions={{
                view: 'manage-biomatric-attedance-settings',
                edit: 'manage-biomatric-attedance-settings',
                delete: 'manage-biomatric-attedance-settings'
              }}
            />
          </div>
        </CardContent>
      </Card>

      <CrudFormModal
        isOpen={isFormModalOpen}
        onClose={() => setIsFormModalOpen(false)}
        onSubmit={handleFormSubmit}
        formConfig={{
          fields: [
            {
              name: 'name',
              label: t('Name'),
              type: 'text',
              required: true,
              placeholder: t('Main Entrance Device')
            },
            {
              name: 'serial_number',
              label: t('Serial Number'),
              type: 'text',
              required: false,
              placeholder: t('SN-001')
            },
            {
              name: 'device_ip',
              label: t('Device IP'),
              type: 'text',
              required: false,
              placeholder: '192.168.1.10'
            },
            {
              name: 'area_id',
              label: t('Area ID'),
              type: 'text',
              required: false,
              placeholder: t('Area-01')
            },
            {
              name: 'status',
              label: t('Status'),
              type: 'select',
              required: true,
              options: [
                { value: '1', label: t('Active') },
                { value: '0', label: t('Inactive') },
              ]
            }
          ]
        }}
        initialData={{
          ...currentItem,
          status: currentItem ? String(currentItem.status) : '1'
        }}
        title={formMode === 'create' ? t('Add Device') : t('Edit Device')}
        mode={formMode}
      />

      <CrudDeleteModal
        isOpen={isDeleteModalOpen}
        onClose={() => setIsDeleteModalOpen(false)}
        onConfirm={handleDeleteConfirm}
        itemName={currentItem?.name || ''}
        entityName={t('device')}
      />
    </SettingsSection>
  );
}
