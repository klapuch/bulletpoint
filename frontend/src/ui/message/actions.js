// @flow
export const RECEIVED_API_ERROR = 'RECEIVED_API_ERROR';
export const DISCARDED_MESSAGE = 'DISCARDED_MESSAGE';
export const RECEIVED_SUCCESS = 'RECEIVED_SUCCESS';

export const receivedApiError = (error: Object) => ({
  type: RECEIVED_API_ERROR,
  content: error.response.data.message,
});

export const receivedError = (error: string) => ({
  type: RECEIVED_API_ERROR,
  content: error,
});

export const receivedSuccess = (content: string) => ({
  type: RECEIVED_SUCCESS,
  content,
});

export const discardedMessage = () => ({
  type: DISCARDED_MESSAGE,
  content: null,
});
